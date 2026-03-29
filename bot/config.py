"""
Configuration du bot BOTCH - Paolo-Wash
=======================================
Ce fichier contient les paramètres sensibles et configurations du bot.
En production, utiliser des variables d'environnement.
"""

import os

# Token du bot Telegram (obtenu via @BotFather)
# En production: BOT_TOKEN = os.getenv("BOT_TOKEN")
BOT_TOKEN = "8638179191:AAH4uQyF0jaDrfnM3hoTiDJfjPGvufdHj_g"

# ID du groupe Telegram pour les notifications
# En production: GROUP_ID = os.getenv("GROUP_ID")
GROUP_ID = "@ton_groupe_ou_ID_numerique"

# Configuration feedback (MP uniquement)
# Liste des admins autorisés à changer les statuts (usernames ou IDs)
FEEDBACK_ADMINS = [
    "paolo_wash",      # Paolo
    "littllmind",      # Aurélien
]

# Liens et coordonnées
# URL calendrier local (pas de service tier)
CALENDAR_API_URL = "http://localhost:5000"
TELEPHONE = "+33 6 XX XX XX XX"  # À remplacer par le vrai numéro

# Services et tarifs Paolo-Wash
SERVICES = {
    "Lavage Intérieur": "À partir de 45€",
    "Lavage Extérieur": "À partir de 35€",
    "Lavage Complet": "À partir de 75€",
    "Détailing Premium": "À partir de 120€",
    "Protection Céramique": "Sur devis",
}

# Message d'accueil
WELCOME_MESSAGE = """
🚗✨ Bienvenue chez **Paolo-Wash** !

Votre véhicule mérite le meilleur soin.
Nous offrons des prestations de nettoyage auto de qualité professionnelle.

*Commandes disponibles:*
🗓️ /reserver — Réserver un créneau
💶 /prix — Voir les tarifs
📞 /contact — Nous contacter
📅 /creneaux — Créneaux disponibles

*Feedback (en MP):*
💡 /besoin — Signaler un besoin
💡 /idee — Proposer une idée
🐛 /bug — Signaler un bug
📋 /liste — Voir les feedbacks
"""

# Message de contact
CONTACT_MESSAGE = """
📞 **Contact Paolo-Wash**

Téléphone : {telephone}

Disponible du lundi au samedi
8h00 - 18h00

Réponse rapide garantie ! 💬
"""

# Message créneaux (placeholder)
CRENEAUX_MESSAGE = """
📅 **Prochains créneaux disponibles**

Les disponibilités sont mises à jour en temps réel sur notre calendrier en ligne.

👉 Réservez directement via /reserver

Pour une demande urgente, contactez-nous au {telephone}
"""

# Messages Feedback
FEEDBACK_MESSAGES = {
    "besoin_added": """
✅ **Besoin enregistré !**

Votre demande a été sauvegardée et sera traitée prochainement.

ID: `{id}`
Statut: 🆕 Nouveau

Utilisez /liste pour voir tous les besoins.
""",
    "idee_added": """
💡 **Idée enregistrée !**

Merci pour votre suggestion ! Elle sera étudiée par l'équipe.

ID: `{id}`
Statut: 🆕 Nouveau

Utilisez /liste pour voir toutes les idées.
""",
    "bug_added": """
🐛 **Bug signalé !**

Merci de nous avoir alerté. Nous allons investiguer rapidement.

ID: `{id}`
Statut: 🆕 Nouveau

Utilisez /liste pour voir tous les bugs.
""",
    "no_description": """
⚠️ **Description requise**

Veuillez ajouter une description après la commande.

*Exemples:*
/besoin Pouvoir voir les stats de réservation
/idee Ajouter un système de notation
/bug Le bouton réserver ne marche pas sur mobile
""",
    "too_short": """
⚠️ **Description trop courte**

La description doit contenir au moins 3 caractères.
""",
    "list_empty": """
📋 **Aucun feedback**

Aucun feedback ne correspond à vos critères.
""",
    "status_updated": """
✅ **Statut mis à jour**

Feedback `{id}` → **{status}**
""",
    "status_not_found": """
❌ **Feedback introuvable**

Aucun feedback trouvé avec l'ID `{id}`.
""",
    "status_no_admin": """
❌ **Accès refusé**

Seuls les administrateurs peuvent changer les statuts.
""",
    "status_usage": """
⚠️ **Utilisation:** /status \u003cID\u003e \u003cstatut\u003e

Statuts disponibles: new, vu, en_cours, fait

Exemple: /status abc123 en_cours
"""
}

# Types de feedback pour affichage
FEEDBACK_EMOJIS = {
    "besoin": "💡",
    "idee": "💡",
    "bug": "🐛",
}

FEEDBACK_LABELS = {
    "besoin": "Besoin",
    "idee": "Idée",
    "bug": "Bug",
}

STATUS_EMOJIS = {
    "new": "🆕",
    "vu": "👀",
    "en_cours": "🔧",
    "fait": "✅",
}

STATUS_LABELS = {
    "new": "Nouveau",
    "vu": "Vu",
    "en_cours": "En cours",
    "fait": "Fait",
}
