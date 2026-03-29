#!/usr/bin/env python3
"""
Module de collecte de feedback pour Paolo-Wash
Gestion CRUD en JSON pour besoins, idées et bugs
Stockage local: bot/data/feedback/*.json
"""

import json
import os
import uuid
from datetime import datetime, timezone
from pathlib import Path
from typing import Optional, List, Dict, Any

# Dossier de stockage
DATA_DIR = Path(__file__).parent / "data" / "feedback"

# Fichiers de stockage
FEEDBACK_FILES = {
    "besoin": DATA_DIR / "besoins.json",
    "idee": DATA_DIR / "idees.json",
    "bug": DATA_DIR / "bugs.json",
}

# Mémoire pour notifications
MEMORY_DIR = Path(__file__).parent.parent / "memory"


def init_storage():
    """Initialise les dossiers et fichiers JSON"""
    DATA_DIR.mkdir(parents=True, exist_ok=True)
    MEMORY_DIR.mkdir(parents=True, exist_ok=True)
    
    for file_path in FEEDBACK_FILES.values():
        if not file_path.exists():
            with open(file_path, 'w', encoding='utf-8') as f:
                json.dump([], f, indent=2, ensure_ascii=False)


def _load_feedback(feedback_type: str) -> List[Dict[str, Any]]:
    """Charge tous les feedbacks d'un type"""
    file_path = FEEDBACK_FILES.get(feedback_type)
    if not file_path or not file_path.exists():
        return []
    
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            return json.load(f)
    except (json.JSONDecodeError, IOError):
        return []


def _save_feedback(feedback_type: str, data: List[Dict[str, Any]]):
    """Sauvegarde les feedbacks d'un type"""
    file_path = FEEDBACK_FILES.get(feedback_type)
    if not file_path:
        return False
    
    try:
        with open(file_path, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=2, ensure_ascii=False)
        return True
    except IOError:
        return False


def _write_notification(feedback: Dict[str, Any]):
    """Écrit une notification dans memory/feedback-new.md"""
    try:
        MEMORY_DIR.mkdir(parents=True, exist_ok=True)
        notification_file = MEMORY_DIR / "feedback-new.md"
        
        timestamp = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S")
        
        content = f"""
## 🆕 Nouveau feedback reçu

- **Type**: {feedback.get('type', 'inconnu')}
- **ID**: `{feedback.get('id')}`
- **Auteur**: {feedback.get('author_name', 'Anonyme')} (@{feedback.get('author', 'unknown')})
- **Date**: {timestamp}
- **Description**: {feedback.get('description', 'N/A')}
- **Statut**: {feedback.get('status', 'new')}

---
"""
        
        # Append au fichier
        with open(notification_file, 'a', encoding='utf-8') as f:
            f.write(content)
        
        return True
    except Exception as e:
        print(f"Erreur écriture notification: {e}")
        return False


def add_feedback(
    feedback_type: str,
    description: str,
    author: str,
    author_name: str = "Anonyme"
) -> Optional[Dict[str, Any]]:
    """
    Ajoute un nouveau feedback.
    
    Args:
        feedback_type: "besoin", "idee" ou "bug"
        description: Texte du feedback
        author: ID Telegram ou username
        author_name: Nom affichable
    
    Returns:
        L'objet feedback créé ou None en cas d'erreur
    """
    if feedback_type not in FEEDBACK_FILES:
        return None
    
    # Validation
    if not description or len(description.strip()) < 3:
        return None
    
    # Crée l'objet
    now = datetime.now(timezone.utc).isoformat()
    feedback = {
        "id": str(uuid.uuid4())[:8],  # ID court: 8 chars
        "type": feedback_type,
        "description": description.strip(),
        "author": author,
        "author_name": author_name,
        "status": "new",
        "created_at": now,
        "updated_at": now
    }
    
    # Charge, ajoute, sauvegarde
    data = _load_feedback(feedback_type)
    data.append(feedback)
    
    if _save_feedback(feedback_type, data):
        # Écrit notification
        _write_notification(feedback)
        return feedback
    
    return None


def get_feedback_by_id(feedback_id: str) -> Optional[Dict[str, Any]]:
    """Récupère un feedback par son ID (cherche dans tous les types)"""
    for feedback_type in FEEDBACK_FILES.keys():
        data = _load_feedback(feedback_type)
        for item in data:
            if item.get("id") == feedback_id:
                return item
    return None


def list_feedback(
    feedback_type: Optional[str] = None,
    status: Optional[str] = None,
    limit: int = 20
) -> List[Dict[str, Any]]:
    """
    Liste les feedbacks avec filtres optionnels.
    
    Args:
        feedback_type: "besoin", "idee", "bug" ou None pour tous
        status: "new", "vu", "en_cours", "fait" ou None pour tous
        limit: Nombre max de résultats
    
    Returns:
        Liste des feedbacks filtrés et triés (plus récents d'abord)
    """
    results = []
    
    types_to_search = [feedback_type] if feedback_type else list(FEEDBACK_FILES.keys())
    
    for ft in types_to_search:
        data = _load_feedback(ft)
        for item in data:
            if status is None or item.get("status") == status:
                results.append(item)
    
    # Tri par date décroissante
    results.sort(key=lambda x: x.get("created_at", ""), reverse=True)
    
    return results[:limit]


def update_status(feedback_id: str, new_status: str) -> bool:
    """
    Met à jour le statut d'un feedback.
    
    Args:
        feedback_id: ID du feedback
        new_status: "new", "vu", "en_cours", "fait"
    
    Returns:
        True si mis à jour, False sinon
    """
    valid_statuses = ["new", "vu", "en_cours", "fait"]
    if new_status not in valid_statuses:
        return False
    
    for feedback_type in FEEDBACK_FILES.keys():
        data = _load_feedback(feedback_type)
        
        for item in data:
            if item.get("id") == feedback_id:
                item["status"] = new_status
                item["updated_at"] = datetime.now(timezone.utc).isoformat()
                
                return _save_feedback(feedback_type, data)
    
    return False


def get_stats() -> Dict[str, int]:
    """Retourne les statistiques des feedbacks"""
    stats = {"total": 0, "new": 0, "vu": 0, "en_cours": 0, "fait": 0}
    
    for feedback_type in FEEDBACK_FILES.keys():
        data = _load_feedback(feedback_type)
        stats["total"] += len(data)
        
        for item in data:
            status = item.get("status", "new")
            if status in stats:
                stats[status] += 1
    
    return stats


def delete_feedback(feedback_id: str) -> bool:
    """Supprime un feedback par son ID"""
    for feedback_type in FEEDBACK_FILES.keys():
        data = _load_feedback(feedback_type)
        original_len = len(data)
        data = [item for item in data if item.get("id") != feedback_id]
        
        if len(data) < original_len:
            return _save_feedback(feedback_type, data)
    
    return False


# Init au chargement du module
init_storage()


if __name__ == "__main__":
    # Tests rapides
    print("=== Test feedback.py ===")
    
    # Ajout
    fb = add_feedback("besoin", "Pouvoir exporter en CSV", "test_user", "Test")
    print(f"Ajouté: {fb}")
    
    # Liste
    all_fb = list_feedback(limit=5)
    print(f"Total: {len(all_fb)} feedbacks")
    
    # Stats
    stats = get_stats()
    print(f"Stats: {stats}")
    
    print("=== Tests OK ===")
