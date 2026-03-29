# 🤖 BOTCH - Bot Telegram Paolo-Wash

Bot Telegram pour le service de lavage auto **Paolo-Wash**.

## 📋 Fonctionnalités

- `/start` — Message d'accueil avec boutons interactifs
- `/reserver` — Lien vers le calendrier SimplyBook.me
- `/prix` — Liste des services avec tarifs
- `/contact` — Coordonnées téléphone
- `/creneaux` — Prochains créneaux disponibles (placeholder)
- **Notifications groupe** — Alerte automatique lors des nouvelles réservations

## 🛠️ Installation

### Prérequis

- Python 3.9+
- Un bot Telegram (créé via [@BotFather](https://t.me/BotFather))
- Token du bot

### Étapes

1. **Cloner ou copier les fichiers** dans votre répertoire :
```bash
cd /chemin/vers/paolo-wash/bot
```

2. **Créer un environnement virtuel** (recommandé) :
```bash
python3 -m venv venv
source venv/bin/activate  # Linux/Mac
# ou
venv\Scripts\activate  # Windows
```

3. **Installer les dépendances** :
```bash
pip install -r requirements.txt
```

4. **Configurer le bot** :
   - Ouvrir `config.py`
   - Remplacer `TON_BOT_TOKEN_ICI` par votre token (@BotFather)
   - Remplacer `@ton_groupe_ou_ID_numerique` par l'ID du groupe pour les notifications
   - Modifier `TELEPHONE` avec le vrai numéro de contact

5. **Lancer le bot** :
```bash
python bot.py
```

## ⚙️ Configuration

### Variables dans `config.py`

| Variable | Description | Exemple |
|----------|-------------|---------|
| `BOT_TOKEN` | Token du bot (obligatoire) | `"123456:ABC..."` |
| `GROUP_ID` | ID du groupe pour notifications | `"@paolo_wash_group"` ou `"-1001234567890"` |
| `SIMPLYBOOK_URL` | URL calendrier SimplyBook.me | `"https://paolowash.simplybook.me"` |
| `TELEPHONE` | Numéro de contact | `"+33 6 12 34 56 78"` |
| `SERVICES` | Dict des services et tarifs | Voir `config.py` |

### Utilisation avec variables d'environnement (production)

Pour plus de sécurité, remplacer dans `config.py` :

```python
import os
BOT_TOKEN = os.getenv("BOT_TOKEN", "TON_BOT_TOKEN_ICI")
GROUP_ID = os.getenv("GROUP_ID", "@ton_groupe_ou_ID_numerique")
```

Puis lancer avec :
```bash
export BOT_TOKEN="votre_token"
export GROUP_ID="@votre_groupe"
python bot.py
```

## 📁 Structure des fichiers

```
paolo-wash/bot/
├── bot.py              # Code principal du bot
├── config.py           # Configuration et constantes
├── requirements.txt    # Dépendances Python
└── README.md           # Ce fichier
```

## 🔔 Notifications Groupe

Pour activer les notifications de nouvelles réservations :

1. Créer un groupe Telegram
2. Ajouter le bot dans le groupe
3. Donner les droits d'admin au bot (optionnel mais recommandé)
4. Récupérer l'ID du groupe :
   - Ajouter [@userinfobot](https://t.me/userinfobot) temporairement
   - Envoyer un message dans le groupe
   - Le bot affichera l'ID (format : `-1001234567890`)
5. Mettre à jour `GROUP_ID` dans `config.py`

**Note** : Les notifications sont envoyées via la fonction `notify_new_reservation()` qui peut être appelée depuis un webhook SimplyBook.me ou manuellement.

## 🚀 Déploiement

### Option 1 : Exécution locale
```bash
python bot.py
```

### Option 2 : PM2 (Node.js process manager)
```bash
npm install -g pm2
pm2 start "python bot.py" --name paolo-wash-bot
pm2 save
pm2 startup
```

### Option 3 : Docker (futur)
Un Dockerfile peut être ajouté pour le déploiement containerisé.

### Option 4 : Hébergement cloud
- **Railway.app** : Gratuit avec limitations
- **Heroku** : Nécessite un worker dyno
- **VPS** (OVH, DigitalOcean, etc.) : Plus stable

## 📝 TODO / Améliorations futures

- [ ] Intégration API SimplyBook.me pour créneaux en temps réel
- [ ] Webhook pour notifications automatiques de réservations
- [ ] Commande `/statut` pour voir les réservations du jour
- [ ] Système de rappel SMS/Email via API externe
- [ ] Interface admin pour modifier les tarifs
- [ ] Statistiques de réservations

## 🐛 Dépannage

### Le bot ne répond pas
- Vérifier le token dans `config.py`
- Vérifier que le bot est démarré (`python bot.py`)
- Vérifier la connexion internet

### Erreur "Chat not found" pour les notifications groupe
- Vérifier que `GROUP_ID` est correct
- Le bot doit être membre du groupe
- Utiliser l'ID numérique (`-100...`) plutôt que le `@nom`

### Commandes non reconnues
- Vérifier que les commandes sont définies dans [@BotFather](https://t.me/BotFather) :
```
start - Démarrer le bot
reserver - Réserver un créneau
prix - Voir les tarifs
contact - Nous contacter
creneaux - Créneaux disponibles
```

## 📞 Support

Pour toute question ou problème :
- Vérifier les logs dans la console
- Consulter la documentation [aiogram](https://docs.aiogram.dev/)
- Ouvrir une issue sur le repo

---

*Développé avec ❤️ pour Paolo-Wash*
