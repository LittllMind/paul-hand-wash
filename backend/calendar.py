#!/usr/bin/env python3
"""
Backend calendrier Paolo-Wash
Simple API REST pour gestion des créneaux
Stockage: SQLite (pas de service tier)
"""

import sqlite3
import json
import os
from datetime import datetime, timedelta
from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

DB_PATH = '/home/aur-lien/.openclaw/workspace/paolo-wash/backend/calendar.db'

def init_db():
    """Initialise la base de données."""
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    
    # Table créneaux disponibles
    c.execute('''
        CREATE TABLE IF NOT EXISTS slots (
            id INTEGER PRIMARY KEY,
            date TEXT NOT NULL,
            time TEXT NOT NULL,
            duration INTEGER DEFAULT 60,
            is_available BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ''')
    
    # Table réservations
    c.execute('''
        CREATE TABLE IF NOT EXISTS reservations (
            id INTEGER PRIMARY KEY,
            slot_id INTEGER,
            client_name TEXT NOT NULL,
            client_phone TEXT NOT NULL,
            service TEXT NOT NULL,
            status TEXT DEFAULT 'confirmed',
            telegram_user_id INTEGER,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (slot_id) REFERENCES slots(id)
        )
    ''')
    
    conn.commit()
    conn.close()

def generate_slots(start_date=None, days=30):
    """Génère les créneaux pour les prochains jours."""
    if start_date is None:
        start_date = datetime.now()
    
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    
    # Horaires : 8h-12h, 14h-18h (créneaux de 1h)
    hours = [8, 9, 10, 11, 14, 15, 16, 17]
    
    for day_offset in range(days):
        date = (start_date + timedelta(days=day_offset)).strftime('%Y-%m-%d')
        
        for hour in hours:
            time = f"{hour:02d}:00"
            
            # Vérifie si existe pas déjà
            c.execute('SELECT id FROM slots WHERE date=? AND time=?', (date, time))
            if not c.fetchone():
                c.execute('INSERT INTO slots (date, time) VALUES (?, ?)', (date, time))
    
    conn.commit()
    conn.close()

@app.route('/api/slots', methods=['GET'])
def get_available_slots():
    """Renvoie les créneaux disponibles."""
    date_from = request.args.get('from', datetime.now().strftime('%Y-%m-%d'))
    days = int(request.args.get('days', 7))
    
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    
    date_to = (datetime.strptime(date_from, '%Y-%m-%d') + timedelta(days=days)).strftime('%Y-%m-%d')
    
    c.execute('''
        SELECT id, date, time FROM slots 
        WHERE date >= ? AND date <= ? AND is_available = 1
        ORDER BY date, time
    ''', (date_from, date_to))
    
    slots = [{'id': row[0], 'date': row[1], 'time': row[2]} for row in c.fetchall()]
    conn.close()
    
    return jsonify({'slots': slots})

@app.route('/api/reserve', methods=['POST'])
def create_reservation():
    """Crée une réservation."""
    data = request.json
    
    required = ['slot_id', 'client_name', 'client_phone', 'service']
    for field in required:
        if field not in data:
            return jsonify({'error': f'Missing field: {field}'}), 400
    
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    
    # Vérifie dispo
    c.execute('SELECT is_available FROM slots WHERE id=?', (data['slot_id'],))
    row = c.fetchone()
    if not row or not row[0]:
        conn.close()
        return jsonify({'error': 'Slot not available'}), 409
    
    # Crée réservation
    c.execute('''
        INSERT INTO reservations (slot_id, client_name, client_phone, service, telegram_user_id)
        VALUES (?, ?, ?, ?, ?)
    ''', (data['slot_id'], data['client_name'], data['client_phone'], 
          data['service'], data.get('telegram_user_id')))
    
    # Marque créneau comme indisponible
    c.execute('UPDATE slots SET is_available=0 WHERE id=?', (data['slot_id'],))
    
    reservation_id = c.lastrowid
    conn.commit()
    conn.close()
    
    return jsonify({'success': True, 'reservation_id': reservation_id}), 201

@app.route('/api/reservations', methods=['GET'])
def get_reservations():
    """Liste des réservations (admin)."""
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    
    c.execute('''
        SELECT r.id, r.client_name, r.client_phone, r.service, r.status,
               s.date, s.time, r.created_at
        FROM reservations r
        JOIN slots s ON r.slot_id = s.id
        WHERE r.status = 'confirmed'
        ORDER BY s.date, s.time
    ''')
    
    reservations = []
    for row in c.fetchall():
        reservations.append({
            'id': row[0],
            'client_name': row[1],
            'client_phone': row[2],
            'service': row[3],
            'status': row[4],
            'date': row[5],
            'time': row[6],
            'created_at': row[7]
        })
    
    conn.close()
    return jsonify({'reservations': reservations})

@app.route('/api/reservations/<int:res_id>/cancel', methods=['POST'])
def cancel_reservation(res_id):
    """Annule une réservation (libère le créneau)."""
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    
    c.execute('SELECT slot_id FROM reservations WHERE id=?', (res_id,))
    row = c.fetchone()
    
    if row:
        slot_id = row[0]
        c.execute('UPDATE reservations SET status="cancelled" WHERE id=?', (res_id,))
        c.execute('UPDATE slots SET is_available=1 WHERE id=?', (slot_id,))
        conn.commit()
        conn.close()
        return jsonify({'success': True})
    
    conn.close()
    return jsonify({'error': 'Reservation not found'}), 404

# ===== FEEDBACK API =====

FEEDBACK_DIR = '/home/aur-lien/.openclaw/workspace/paolo-wash/bot/data/feedback'

@app.route('/api/feedback', methods=['GET'])
def get_feedback():
    """Récupère les feedbacks pour l'interface admin (endpoint public)."""
    feedback_type = request.args.get('type')  # besoin, idee, bug
    status = request.args.get('status')  # new, vu, en_cours, fait
    limit = request.args.get('limit', 50, type=int)
    
    result = []
    
    # Détermine quels fichiers lire
    files_to_read = []
    if feedback_type == 'besoin':
        files_to_read = ['besoins.json']
    elif feedback_type == 'idee':
        files_to_read = ['idees.json']
    elif feedback_type == 'bug':
        files_to_read = ['bugs.json']
    else:
        files_to_read = ['besoins.json', 'idees.json', 'bugs.json']
    
    for filename in files_to_read:
        filepath = os.path.join(FEEDBACK_DIR, filename)
        if os.path.exists(filepath):
            try:
                with open(filepath, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                    for item in data:
                        # Filtrer par statut si demandé
                        if status is None or item.get('status') == status:
                            result.append(item)
            except (json.JSONDecodeError, IOError):
                continue
    
    # Trier par date décroissante
    result.sort(key=lambda x: x.get('created_at', ''), reverse=True)
    
    # Limiter
    result = result[:limit]
    
    return jsonify({
        'feedback': result,
        'count': len(result)
    })


# Import et enregistrement de l'admin API
from api_admin import api_admin, db

# Enregistre les routes admin
app.register_blueprint(api_admin)

if __name__ == '__main__':
    init_db()
    db.init_tables()  # Init les nouvelles tables
    generate_slots()
    print("🚀 Backend calendrier démarré sur http://localhost:5000")
    print("📊 Admin API disponible sur http://localhost:5000/api/admin")
    print("🔑 Token: Bearer paolo-secret-2025")
    app.run(host='0.0.0.0', port=5000, debug=True)