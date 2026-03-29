#!/usr/bin/env python3
"""
API Admin Paolo-Wash V2
Endpoints pour gestion des créneaux et journées de travail
"""

import os
from datetime import datetime, date, time
from functools import wraps
from flask import Flask, request, jsonify, Blueprint
from flask_cors import CORS

from models import db, WorkingDay, TimeSlot

# Simple auth token (à remplacer par JWT en prod)
ADMIN_TOKEN = os.getenv('ADMIN_TOKEN', 'paolo-secret-2025')

api_admin = Blueprint('api_admin', __name__, url_prefix='/api/admin')


def require_auth(f):
    """Décorateur pour vérifier le token admin"""
    @wraps(f)
    def decorated_function(*args, **kwargs):
        auth_header = request.headers.get('Authorization')
        if not auth_header:
            return jsonify({'error': 'Missing Authorization header'}), 401
        
        # Format: Bearer <token>
        parts = auth_header.split()
        if len(parts) != 2 or parts[0] != 'Bearer':
            return jsonify({'error': 'Invalid Authorization format. Use: Bearer <token>'}), 401
        
        token = parts[1]
        if token != ADMIN_TOKEN:
            return jsonify({'error': 'Invalid token'}), 401
        
        return f(*args, **kwargs)
    return decorated_function


# ===== AUTH =====

@api_admin.route('/login', methods=['POST'])
def admin_login():
    """Authentification admin simple"""
    data = request.json or {}
    password = data.get('password')
    
    # Simple auth (à améliorer avec hash bcrypt)
    if password == 'paolo2025':
        return jsonify({
            'success': True,
            'token': ADMIN_TOKEN,
            'message': 'Authentication successful'
        })
    
    return jsonify({'error': 'Invalid credentials'}), 401


# ===== WORKING DAYS =====

@api_admin.route('/working-days', methods=['POST'])
@require_auth
def create_working_day():
    """Crée une nouvelle journée de travail avec génération optionnelle de créneaux"""
    data = request.json or {}
    
    try:
        # Parse date
        date_str = data.get('date')
        if not date_str:
            return jsonify({'error': 'date is required (YYYY-MM-DD)'}), 400
        
        wd_date = datetime.strptime(date_str, '%Y-%m-%d').date()
        
        # Vérifie si jour existe déjà
        existing = db.get_working_day_by_date(wd_date)
        if existing:
            return jsonify({'error': f'Working day for {date_str} already exists'}), 409
        
        # Parse plages horaires
        def parse_time(t):
            return datetime.strptime(t, '%H:%M').time() if t else None
        
        wd = WorkingDay(
            date=wd_date,
            is_available=data.get('is_available', True),
            morning_start=parse_time(data.get('morning_start')),
            morning_end=parse_time(data.get('morning_end')),
            afternoon_start=parse_time(data.get('afternoon_start')),
            afternoon_end=parse_time(data.get('afternoon_end')),
            location=data.get('location'),
            notes=data.get('notes')
        )
        
        wd = db.create_working_day(wd)
        
        # Génère les créneaux si demandé
        slots = []
        if data.get('generate_slots', True) and wd.is_available:
            interval = data.get('slot_interval_minutes', 60)
            slots = db.generate_time_slots(wd.id, interval)
        
        return jsonify({
            'success': True,
            'working_day': wd.to_dict(),
            'slots_generated': len(slots),
            'slots': [s.to_dict() for s in slots]
        }), 201
        
    except ValueError as e:
        return jsonify({'error': f'Invalid date/time format: {str(e)}'}), 400
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@api_admin.route('/working-days', methods=['GET'])
@require_auth
def list_working_days():
    """Liste les journées de travail, filtrable par mois"""
    month = request.args.get('month')  # Format: YYYY-MM
    
    days = db.list_working_days(month)
    
    # Enrichit avec les statistiques de créneaux
    result = []
    for wd in days:
        slots = db.get_time_slots_by_day(wd.id)
        available_slots = sum(1 for s in slots if s.is_available and not s.is_blocked)
        
        wd_dict = wd.to_dict()
        wd_dict['stats'] = {
            'total_slots': len(slots),
            'available_slots': available_slots,
            'blocked_slots': sum(1 for s in slots if s.is_blocked),
            'booked_slots': sum(1 for s in slots if s.booking_id)
        }
        result.append(wd_dict)
    
    return jsonify({
        'working_days': result,
        'count': len(result)
    })


@api_admin.route('/working-days/<int:wd_id>', methods=['GET'])
@require_auth
def get_working_day(wd_id):
    """Récupère une journée spécifique avec ses créneaux"""
    wd = db.get_working_day(wd_id)
    if not wd:
        return jsonify({'error': 'Working day not found'}), 404
    
    slots = db.get_time_slots_by_day(wd_id)
    
    return jsonify({
        'working_day': wd.to_dict(),
        'slots': [s.to_dict() for s in slots]
    })


@api_admin.route('/working-days/<int:wd_id>', methods=['PUT'])
@require_auth
def update_working_day(wd_id):
    """Modifie une journée de travail"""
    wd = db.get_working_day(wd_id)
    if not wd:
        return jsonify({'error': 'Working day not found'}), 404
    
    data = request.json or {}
    
    try:
        def parse_time(t):
            return datetime.strptime(t, '%H:%M').time() if t else None
        
        if 'date' in data:
            wd.date = datetime.strptime(data['date'], '%Y-%m-%d').date()
        if 'is_available' in data:
            wd.is_available = data['is_available']
        if 'morning_start' in data:
            wd.morning_start = parse_time(data['morning_start'])
        if 'morning_end' in data:
            wd.morning_end = parse_time(data['morning_end'])
        if 'afternoon_start' in data:
            wd.afternoon_start = parse_time(data['afternoon_start'])
        if 'afternoon_end' in data:
            wd.afternoon_end = parse_time(data['afternoon_end'])
        if 'location' in data:
            wd.location = data['location']
        if 'notes' in data:
            wd.notes = data['notes']
        
        wd = db.update_working_day(wd)
        
        # Régénère les créneaux si demandé
        slots = []
        if data.get('regenerate_slots', False):
            # Supprime les anciens créneaux non réservés
            old_slots = db.get_time_slots_by_day(wd_id)
            for s in old_slots:
                if not s.booking_id:
                    db.delete_time_slot(s.id)
            
            # Génère les nouveaux
            interval = data.get('slot_interval_minutes', 60)
            slots = db.generate_time_slots(wd_id, interval)
        
        return jsonify({
            'success': True,
            'working_day': wd.to_dict(),
            'slots_regenerated': len(slots) if data.get('regenerate_slots') else 0
        })
        
    except ValueError as e:
        return jsonify({'error': f'Invalid format: {str(e)}'}), 400


@api_admin.route('/working-days/<int:wd_id>', methods=['DELETE'])
@require_auth
def delete_working_day(wd_id):
    """Supprime une journée de travail et ses créneaux"""
    wd = db.get_working_day(wd_id)
    if not wd:
        return jsonify({'error': 'Working day not found'}), 404
    
    deleted = db.delete_working_day(wd_id)
    
    return jsonify({
        'success': deleted,
        'message': 'Working day deleted'
    })


# ===== TIME SLOTS =====

@api_admin.route('/slots', methods=['GET'])
@require_auth
def get_slots():
    """Récupère les créneaux d'une journée spécifique"""
    date_str = request.args.get('date')
    working_day_id = request.args.get('working_day_id', type=int)
    
    if not date_str and not working_day_id:
        return jsonify({'error': 'Provide either date or working_day_id'}), 400
    
    if date_str:
        try:
            wd_date = datetime.strptime(date_str, '%Y-%m-%d').date()
            wd = db.get_working_day_by_date(wd_date)
            if not wd:
                return jsonify({'slots': [], 'message': 'No working day for this date'})
            working_day_id = wd.id
        except ValueError:
            return jsonify({'error': 'Invalid date format (YYYY-MM-DD)'}), 400
    
    slots = db.get_time_slots_by_day(working_day_id)
    
    # Enrichit avec les infos réservation
    conn = db.get_connection()
    c = conn.cursor()
    
    result = []
    for slot in slots:
        slot_dict = slot.to_dict()
        if slot.booking_id:
            c.execute('''
                SELECT client_name, client_phone, service, status 
                FROM reservations WHERE id=?
            ''', (slot.booking_id,))
            booking = c.fetchone()
            if booking:
                slot_dict['booking'] = {
                    'client_name': booking[0],
                    'client_phone': booking[1],
                    'service': booking[2],
                    'status': booking[3]
                }
        result.append(slot_dict)
    
    conn.close()
    
    return jsonify({
        'slots': result,
        'count': len(result)
    })


@api_admin.route('/slots/<int:slot_id>', methods=['PUT'])
@require_auth
def update_slot(slot_id):
    """Modifie un créneau (bloquer/déblocher)"""
    slot = db.get_time_slot(slot_id)
    if not slot:
        return jsonify({'error': 'Slot not found'}), 404
    
    data = request.json or {}
    
    if 'is_available' in data:
        slot.is_available = data['is_available']
    if 'is_blocked' in data:
        slot.is_blocked = data['is_blocked']
    
    slot = db.update_time_slot(slot)
    
    return jsonify({
        'success': True,
        'slot': slot.to_dict()
    })


@api_admin.route('/slots/generate', methods=['POST'])
@require_auth
def generate_slots():
    """Génère les créneaux pour une journée"""
    data = request.json or {}
    
    working_day_id = data.get('working_day_id')
    date_str = data.get('date')
    interval_minutes = data.get('interval_minutes', 60)
    
    if not working_day_id and not date_str:
        return jsonify({'error': 'Provide working_day_id or date'}), 400
    
    if date_str:
        try:
            wd_date = datetime.strptime(date_str, '%Y-%m-%d').date()
            wd = db.get_working_day_by_date(wd_date)
            if not wd:
                return jsonify({'error': 'Working day not found for this date'}), 404
            working_day_id = wd.id
        except ValueError:
            return jsonify({'error': 'Invalid date format'}), 400
    
    # Supprime les anciens créneaux non réservés
    old_slots = db.get_time_slots_by_day(working_day_id)
    deleted_count = 0
    for s in old_slots:
        if not s.booking_id:
            db.delete_time_slot(s.id)
            deleted_count += 1
    
    # Génère les nouveaux
    slots = db.generate_time_slots(working_day_id, interval_minutes)
    
    return jsonify({
        'success': True,
        'deleted_old': deleted_count,
        'generated': len(slots),
        'slots': [s.to_dict() for s in slots]
    })


@api_admin.route('/slots/<int:slot_id>', methods=['DELETE'])
@require_auth
def delete_slot(slot_id):
    """Supprime un créneau (s'il n'est pas réservé)"""
    slot = db.get_time_slot(slot_id)
    if not slot:
        return jsonify({'error': 'Slot not found'}), 404
    
    if slot.booking_id:
        return jsonify({'error': 'Cannot delete a booked slot. Cancel the reservation first.'}), 409
    
    deleted = db.delete_time_slot(slot_id)
    
    return jsonify({
        'success': deleted,
        'message': 'Slot deleted'
    })


# ===== DASHBOARD STATS =====

@api_admin.route('/stats', methods=['GET'])
@require_auth
def get_stats():
    """Statistiques dashboard admin"""
    conn = db.get_connection()
    c = conn.cursor()
    
    # Stats globales
    c.execute('SELECT COUNT(*) FROM working_days WHERE is_available=1')
    available_days = c.fetchone()[0]
    
    c.execute('SELECT COUNT(*) FROM time_slots')
    total_slots = c.fetchone()[0]
    
    c.execute('SELECT COUNT(*) FROM time_slots WHERE is_available=1 AND is_blocked=0 AND booking_id IS NULL')
    available_slots = c.fetchone()[0]
    
    c.execute('SELECT COUNT(*) FROM reservations WHERE status="confirmed"')
    total_bookings = c.fetchone()[0]
    
    # Réservations du jour
    today = date.today().isoformat()
    c.execute('''
        SELECT COUNT(*) FROM reservations r
        JOIN time_slots ts ON r.slot_id = ts.id
        JOIN working_days wd ON ts.working_day_id = wd.id
        WHERE wd.date = ? AND r.status = "confirmed"
    ''', (today,))
    today_bookings = c.fetchone()[0]
    
    conn.close()
    
    return jsonify({
        'stats': {
            'available_days': available_days,
            'total_slots': total_slots,
            'available_slots': available_slots,
            'total_bookings': total_bookings,
            'today_bookings': today_bookings
        }
    })


# ===== PUBLIC API (no auth required) =====

@api_admin.route('/public/slots', methods=['GET'])
def public_get_slots():
    """Récupère les créneaux disponibles (public, pour le site client)"""
    date_from = request.args.get('from', date.today().isoformat())
    days = request.args.get('days', 7, type=int)
    
    try:
        from_date = datetime.strptime(date_from, '%Y-%m-%d').date()
        to_date = from_date + __import__('datetime').timedelta(days=days)
        
        conn = db.get_connection()
        c = conn.cursor()
        
        c.execute('''
            SELECT ts.id, wd.date, ts.time, ts.is_available, ts.is_blocked, ts.booking_id
            FROM time_slots ts
            JOIN working_days wd ON ts.working_day_id = wd.id
            WHERE wd.date >= ? AND wd.date <= ?
            AND wd.is_available = 1
            AND ts.is_available = 1
            AND ts.is_blocked = 0
            AND ts.booking_id IS NULL
            ORDER BY wd.date, ts.time
        ''', (from_date.isoformat(), to_date.isoformat()))
        
        slots = [{'id': row[0], 'date': row[1], 'time': row[2]} 
                 for row in c.fetchall()]
        
        conn.close()
        
        return jsonify({'slots': slots, 'count': len(slots)})
        
    except ValueError as e:
        return jsonify({'error': 'Invalid date format'}), 400


# ===== APP FACTORY =====

def create_app():
    """Factory pour créer l'app Flask"""
    app = Flask(__name__)
    CORS(app)
    
    # Init DB
    db.init_tables()
    
    # Register blueprints
    app.register_blueprint(api_admin)
    
    return app


if __name__ == '__main__':
    app = create_app()
    print("🚀 Admin API démarrée sur http://localhost:5000")
    print("🔑 Token admin: Bearer paolo-secret-2025")
    app.run(host='0.0.0.0', port=5000, debug=True)