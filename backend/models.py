#!/usr/bin/env python3
"""
Modèles de données Paolo-Wash
SQLAlchemy-like avec SQLite natif
"""

import sqlite3
import json
from datetime import datetime, date, time, timedelta
from dataclasses import dataclass, asdict
from typing import Optional, List
import uuid

DB_PATH = '/home/aur-lien/.openclaw/workspace/paolo-wash/backend/calendar.db'


@dataclass
class WorkingDay:
    """Représente une journée de travail de Paolo"""
    id: Optional[int] = None
    date: Optional[date] = None
    is_available: bool = True
    morning_start: Optional[time] = None
    morning_end: Optional[time] = None
    afternoon_start: Optional[time] = None
    afternoon_end: Optional[time] = None
    location: Optional[str] = None
    notes: Optional[str] = None
    created_at: Optional[datetime] = None
    
    def to_dict(self):
        return {
            'id': self.id,
            'date': self.date.isoformat() if self.date else None,
            'is_available': self.is_available,
            'morning_start': self.morning_start.isoformat() if self.morning_start else None,
            'morning_end': self.morning_end.isoformat() if self.morning_end else None,
            'afternoon_start': self.afternoon_start.isoformat() if self.afternoon_start else None,
            'afternoon_end': self.afternoon_end.isoformat() if self.afternoon_end else None,
            'location': self.location,
            'notes': self.notes,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }
    
    @classmethod
    def from_row(cls, row):
        """Crée un objet depuis une ligne SQLite"""
        if not row:
            return None
        return cls(
            id=row[0],
            date=datetime.strptime(row[1], '%Y-%m-%d').date() if row[1] else None,
            is_available=bool(row[2]),
            morning_start=datetime.strptime(row[3], '%H:%M').time() if row[3] else None,
            morning_end=datetime.strptime(row[4], '%H:%M').time() if row[4] else None,
            afternoon_start=datetime.strptime(row[5], '%H:%M').time() if row[5] else None,
            afternoon_end=datetime.strptime(row[6], '%H:%M').time() if row[6] else None,
            location=row[7],
            notes=row[8],
            created_at=datetime.fromisoformat(row[9]) if row[9] else None
        )


@dataclass
class TimeSlot:
    """Créneau horaire spécifique dans une journée"""
    id: Optional[int] = None
    working_day_id: Optional[int] = None
    time: Optional[str] = None  # HH:MM
    is_available: bool = True
    is_blocked: bool = False
    booking_id: Optional[int] = None
    created_at: Optional[datetime] = None
    
    def to_dict(self):
        return {
            'id': self.id,
            'working_day_id': self.working_day_id,
            'time': self.time,
            'is_available': self.is_available,
            'is_blocked': self.is_blocked,
            'booking_id': self.booking_id,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }
    
    @classmethod
    def from_row(cls, row):
        if not row:
            return None
        return cls(
            id=row[0],
            working_day_id=row[1],
            time=row[2],
            is_available=bool(row[3]),
            is_blocked=bool(row[4]),
            booking_id=row[5],
            created_at=datetime.fromisoformat(row[6]) if row[6] else None
        )


class Database:
    """Gestionnaire de base de données"""
    
    def __init__(self, db_path: str = DB_PATH):
        self.db_path = db_path
    
    def get_connection(self):
        conn = sqlite3.connect(self.db_path)
        conn.row_factory = sqlite3.Row
        return conn
    
    def init_tables(self):
        """Initialise toutes les tables"""
        conn = self.get_connection()
        c = conn.cursor()
        
        # Table legacy slots (pour compatibilité)
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
        
        # Table legacy reservations
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
        
        # Nouvelle table working_days
        c.execute('''
            CREATE TABLE IF NOT EXISTS working_days (
                id INTEGER PRIMARY KEY,
                date TEXT UNIQUE NOT NULL,
                is_available BOOLEAN DEFAULT 1,
                morning_start TEXT,
                morning_end TEXT,
                afternoon_start TEXT,
                afternoon_end TEXT,
                location TEXT,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # Nouvelle table time_slots
        c.execute('''
            CREATE TABLE IF NOT EXISTS time_slots (
                id INTEGER PRIMARY KEY,
                working_day_id INTEGER NOT NULL,
                time TEXT NOT NULL,
                is_available BOOLEAN DEFAULT 1,
                is_blocked BOOLEAN DEFAULT 0,
                booking_id INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (working_day_id) REFERENCES working_days(id),
                FOREIGN KEY (booking_id) REFERENCES reservations(id),
                UNIQUE(working_day_id, time)
            )
        ''')
        
        conn.commit()
        conn.close()
        print("✅ Tables initialisées")
    
    # ===== WORKING DAYS CRUD =====
    
    def create_working_day(self, wd: WorkingDay) -> WorkingDay:
        """Crée une nouvelle journée de travail"""
        conn = self.get_connection()
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO working_days (date, is_available, morning_start, morning_end,
                                      afternoon_start, afternoon_end, location, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ''', (
            wd.date.isoformat() if wd.date else None,
            wd.is_available,
            wd.morning_start.strftime('%H:%M') if wd.morning_start else None,
            wd.morning_end.strftime('%H:%M') if wd.morning_end else None,
            wd.afternoon_start.strftime('%H:%M') if wd.afternoon_start else None,
            wd.afternoon_end.strftime('%H:%M') if wd.afternoon_end else None,
            wd.location,
            wd.notes
        ))
        
        wd.id = c.lastrowid
        conn.commit()
        conn.close()
        return wd
    
    def get_working_day(self, working_day_id: int) -> Optional[WorkingDay]:
        """Récupère une journée par ID"""
        conn = self.get_connection()
        c = conn.cursor()
        c.execute('SELECT * FROM working_days WHERE id=?', (working_day_id,))
        row = c.fetchone()
        conn.close()
        return WorkingDay.from_row(row) if row else None
    
    def get_working_day_by_date(self, date_obj: date) -> Optional[WorkingDay]:
        """Récupère une journée par date"""
        conn = self.get_connection()
        c = conn.cursor()
        c.execute('SELECT * FROM working_days WHERE date=?', (date_obj.isoformat(),))
        row = c.fetchone()
        conn.close()
        return WorkingDay.from_row(row) if row else None
    
    def list_working_days(self, month: str = None) -> List[WorkingDay]:
        """Liste les journées de travail, optionnellement filtrées par mois (YYYY-MM)"""
        conn = self.get_connection()
        c = conn.cursor()
        
        if month:
            c.execute('''
                SELECT * FROM working_days 
                WHERE date LIKE ? 
                ORDER BY date
            ''', (f'{month}%',))
        else:
            c.execute('SELECT * FROM working_days ORDER BY date')
        
        rows = c.fetchall()
        conn.close()
        return [WorkingDay.from_row(row) for row in rows]
    
    def update_working_day(self, wd: WorkingDay) -> WorkingDay:
        """Met à jour une journée de travail"""
        conn = self.get_connection()
        c = conn.cursor()
        
        c.execute('''
            UPDATE working_days 
            SET date=?, is_available=?, morning_start=?, morning_end=?,
                afternoon_start=?, afternoon_end=?, location=?, notes=?
            WHERE id=?
        ''', (
            wd.date.isoformat() if wd.date else None,
            wd.is_available,
            wd.morning_start.strftime('%H:%M') if wd.morning_start else None,
            wd.morning_end.strftime('%H:%M') if wd.morning_end else None,
            wd.afternoon_start.strftime('%H:%M') if wd.afternoon_start else None,
            wd.afternoon_end.strftime('%H:%M') if wd.afternoon_end else None,
            wd.location,
            wd.notes,
            wd.id
        ))
        
        conn.commit()
        conn.close()
        return wd
    
    def delete_working_day(self, working_day_id: int) -> bool:
        """Supprime une journée et ses créneaux associés"""
        conn = self.get_connection()
        c = conn.cursor()
        
        # Supprime d'abord les time_slots associés
        c.execute('DELETE FROM time_slots WHERE working_day_id=?', (working_day_id,))
        c.execute('DELETE FROM working_days WHERE id=?', (working_day_id,))
        
        deleted = c.rowcount > 0
        conn.commit()
        conn.close()
        return deleted
    
    # ===== TIME SLOTS CRUD =====
    
    def create_time_slot(self, ts: TimeSlot) -> TimeSlot:
        """Crée un nouveau créneau horaire"""
        conn = self.get_connection()
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO time_slots (working_day_id, time, is_available, is_blocked, booking_id)
            VALUES (?, ?, ?, ?, ?)
        ''', (ts.working_day_id, ts.time, ts.is_available, ts.is_blocked, ts.booking_id))
        
        ts.id = c.lastrowid
        conn.commit()
        conn.close()
        return ts
    
    def get_time_slot(self, slot_id: int) -> Optional[TimeSlot]:
        """Récupère un créneau par ID"""
        conn = self.get_connection()
        c = conn.cursor()
        c.execute('SELECT * FROM time_slots WHERE id=?', (slot_id,))
        row = c.fetchone()
        conn.close()
        return TimeSlot.from_row(row) if row else None
    
    def get_time_slots_by_day(self, working_day_id: int) -> List[TimeSlot]:
        """Récupère tous les créneaux d'une journée"""
        conn = self.get_connection()
        c = conn.cursor()
        c.execute('''
            SELECT * FROM time_slots 
            WHERE working_day_id=? 
            ORDER BY time
        ''', (working_day_id,))
        rows = c.fetchall()
        conn.close()
        return [TimeSlot.from_row(row) for row in rows]
    
    def update_time_slot(self, ts: TimeSlot) -> TimeSlot:
        """Met à jour un créneau"""
        conn = self.get_connection()
        c = conn.cursor()
        
        c.execute('''
            UPDATE time_slots 
            SET working_day_id=?, time=?, is_available=?, is_blocked=?, booking_id=?
            WHERE id=?
        ''', (ts.working_day_id, ts.time, ts.is_available, ts.is_blocked, ts.booking_id, ts.id))
        
        conn.commit()
        conn.close()
        return ts
    
    def delete_time_slot(self, slot_id: int) -> bool:
        """Supprime un créneau"""
        conn = self.get_connection()
        c = conn.cursor()
        c.execute('DELETE FROM time_slots WHERE id=?', (slot_id,))
        deleted = c.rowcount > 0
        conn.commit()
        conn.close()
        return deleted
    
    # ===== GENERATION AUTO =====
    
    def generate_time_slots(self, working_day_id: int, interval_minutes: int = 60) -> List[TimeSlot]:
        """
        Génère automatiquement les créneaux pour une journée
        selon les plages horaires définies
        """
        wd = self.get_working_day(working_day_id)
        if not wd or not wd.is_available:
            return []
        
        slots = []
        
        # Génère créneaux matin
        if wd.morning_start and wd.morning_end:
            current = datetime.combine(wd.date, wd.morning_start)
            end = datetime.combine(wd.date, wd.morning_end)
            
            while current < end:
                time_str = current.strftime('%H:%M')
                ts = TimeSlot(
                    working_day_id=working_day_id,
                    time=time_str,
                    is_available=True,
                    is_blocked=False
                )
                try:
                    ts = self.create_time_slot(ts)
                    slots.append(ts)
                except sqlite3.IntegrityError:
                    # Créneau existe déjà
                    pass
                current += timedelta(minutes=interval_minutes)
        
        # Génère créneaux après-midi
        if wd.afternoon_start and wd.afternoon_end:
            current = datetime.combine(wd.date, wd.afternoon_start)
            end = datetime.combine(wd.date, wd.afternoon_end)
            
            while current < end:
                time_str = current.strftime('%H:%M')
                ts = TimeSlot(
                    working_day_id=working_day_id,
                    time=time_str,
                    is_available=True,
                    is_blocked=False
                )
                try:
                    ts = self.create_time_slot(ts)
                    slots.append(ts)
                except sqlite3.IntegrityError:
                    pass
                current += timedelta(minutes=interval_minutes)
        
        return slots


# Singleton pour usage facile
db = Database()


if __name__ == '__main__':
    # Test d'initialisation
    db.init_tables()
    print("✅ Base de données initialisée")