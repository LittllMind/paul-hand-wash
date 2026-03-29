"""
BOTCH - Bot Telegram pour Paolo-Wash
===================================
Bot de réservation et informations pour un service de lavage auto.

Commandes disponibles:
- /start : Message d'accueil avec boutons
- /reserver : Lien vers calendrier SimplyBook.me
- /prix : Liste des services avec tarifs
- /contact : Coordonnées téléphone
- /creneaux : Prochains créneaux disponibles (placeholder)

Notifications groupe: Alertes nouvelles réservations
"""

import asyncio
import logging
from aiogram import Bot, Dispatcher, types, F
from aiogram.filters import Command
from aiogram.types import ReplyKeyboardMarkup, KeyboardButton, InlineKeyboardMarkup, InlineKeyboardButton
from aiogram.enums import ParseMode

from config import (
    BOT_TOKEN,
    GROUP_ID,
    CALENDAR_API_URL,
    TELEPHONE,
    SERVICES,
    WELCOME_MESSAGE,
    CONTACT_MESSAGE,
    CRENEAUX_MESSAGE,
    FEEDBACK_MESSAGES,
    FEEDBACK_ADMINS,
    FEEDBACK_EMOJIS,
    FEEDBACK_LABELS,
    STATUS_EMOJIS,
    STATUS_LABELS,
)

from feedback import add_feedback, list_feedback, update_status, get_feedback_by_id

import aiohttp

# Configuration du logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialisation du bot et dispatcher
bot = Bot(token=BOT_TOKEN)
dp = Dispatcher()

# ============ CLAVIERS ============

def get_main_keyboard() -> ReplyKeyboardMarkup:
    """Retourne le clavier principal avec boutons de commandes."""
    keyboard = [
        [KeyboardButton(text="🗓️ Réserver"), KeyboardButton(text="💶 Tarifs")],
        [KeyboardButton(text="📞 Contact"), KeyboardButton(text="📅 Créneaux")],
    ]
    return ReplyKeyboardMarkup(
        keyboard=keyboard,
        resize_keyboard=True,
        input_field_placeholder="Choisissez une option..."
    )


def get_reserve_inline() -> InlineKeyboardMarkup:
    """Bouton inline pour la réservation (site local)."""
    return InlineKeyboardMarkup(
        inline_keyboard=[
            [InlineKeyboardButton(text="🗓️ Réserver sur le site", url="http://localhost:8000")],
        ]
    )


# ============ HANDLERS COMMANDES ============

@dp.message(Command("start"))
async def cmd_start(message: types.Message):
    """Commande /start - Accueil avec boutons."""
    logger.info(f"Nouvel utilisateur: {message.from_user.id} ({message.from_user.full_name})")
    
    await message.answer(
        WELCOME_MESSAGE,
        parse_mode=ParseMode.MARKDOWN,
        reply_markup=get_main_keyboard()
    )


@dp.message(Command("reserver"))
async def cmd_reserver(message: types.Message):
    """Commande /reserver - Lien vers calendrier SimplyBook.me."""
    text = (
        "🗓️ **Réserver un créneau**\n\n"
        "Cliquez ci-dessous pour accéder à notre calendrier en ligne "
        "et choisir votre créneau préféré :\n\n"
        "✅ Disponibilités en temps réel\n"
        "✅ Confirmation immédiate\n"
        "✅ Paiement sécurisé"
    )
    
    await message.answer(
        text,
        parse_mode=ParseMode.MARKDOWN,
        reply_markup=get_reserve_inline()
    )


@dp.message(Command("prix"))
async def cmd_prix(message: types.Message):
    """Commande /prix - Liste des services avec tarifs."""
    services_text = "\n".join(
        [f"• **{service}** : {prix}" for service, prix in SERVICES.items()]
    )
    
    text = (
        "💶 **Nos Tarifs**\n\n"
        f"{services_text}\n\n"
        "💡 *Les prix peuvent varier selon la taille du véhicule "
        "et l'état du véhicule.*\n\n"
        "👉 Pour un devis personnalisé : /contact"
    )
    
    await message.answer(text, parse_mode=ParseMode.MARKDOWN)


@dp.message(Command("contact"))
async def cmd_contact(message: types.Message):
    """Commande /contact - Coordonnées téléphone."""
    text = CONTACT_MESSAGE.format(telephone=TELEPHONE)
    
    # Créer un bouton pour appeler directement
    contact_kb = InlineKeyboardMarkup(
        inline_keyboard=[
            [InlineKeyboardButton(text="📞 Appeler maintenant", url=f"tel:{TELEPHONE.replace(' ', '')}")],
        ]
    )
    
    await message.answer(
        text,
        parse_mode=ParseMode.MARKDOWN,
        reply_markup=contact_kb
    )


@dp.message(Command("creneaux"))
async def cmd_creneaux(message: types.Message):
    """Commande /creneaux - Prochains créneaux disponibles depuis l'API locale."""
    try:
        async with aiohttp.ClientSession() as session:
            async with session.get(f"{CALENDAR_API_URL}/api/slots?days=3") as resp:
                if resp.status == 200:
                    data = await resp.json()
                    slots = data.get('slots', [])
                    
                    if not slots:
                        text = "📅 **Aucun créneau disponible**\n\nContactez directement Paolo."
                    else:
                        text = "📅 **Prochains créneaux disponibles**\n\n"
                        current_date = None
                        for slot in slots[:10]:  # Max 10 créneaux
                            if slot['date'] != current_date:
                                text += f"\n*{slot['date']}*\n"
                                current_date = slot['date']
                            text += f"  • {slot['time']}\n"
                        
                        text += f"\n👉 Réservez sur notre site ou par /reserver"
                else:
                    text = "⚠️ Service temporairement indisponible.\nContactez Paolo directement."
    except Exception as e:
        logger.error(f"Erreur fetch créneaux: {e}")
        text = "⚠️ Impossible de récupérer les créneaux.\nLe service est-il démarré ?"
    
    await message.answer(text, parse_mode=ParseMode.MARKDOWN)


# ============ HANDLERS BOUTONS CLAVIER ============

@dp.message(F.text == "🗓️ Réserver")
async def btn_reserver(message: types.Message):
    """Handler bouton Réserver."""
    await cmd_reserver(message)


@dp.message(F.text == "💶 Tarifs")
async def btn_tarifs(message: types.Message):
    """Handler bouton Tarifs."""
    await cmd_prix(message)


@dp.message(F.text == "📞 Contact")
async def btn_contact(message: types.Message):
    """Handler bouton Contact."""
    await cmd_contact(message)


@dp.message(F.text == "📅 Créneaux")
async def btn_creneaux(message: types.Message):
    """Handler bouton Créneaux."""
    await cmd_creneaux(message)


# ============ COMMANDES FEEDBACK (MP UNIQUEMENT) ============

def _get_author_name(user: types.User) -> str:
    """Retourne le nom affichable d'un utilisateur."""
    if user.first_name and user.last_name:
        return f"{user.first_name} {user.last_name}"
    return user.first_name or user.username or "Anonyme"


def _format_feedback_list(feedbacks: list, feedback_type: str = None) -> str:
    """Formate la liste des feedbacks pour affichage."""
    if not feedbacks:
        return FEEDBACK_MESSAGES["list_empty"]
    
    lines = ["📋 **Feedbacks**\n"]
    
    for fb in feedbacks:
        emoji = FEEDBACK_EMOJIS.get(fb.get("type"), "📝")
        status_emoji = STATUS_EMOJIS.get(fb.get("status", "new"), "🆕")
        type_label = FEEDBACK_LABELS.get(fb.get("type"), fb.get("type", "?"))
        status_label = STATUS_LABELS.get(fb.get("status", "new"), fb.get("status", "new"))
        
        # Truncate description if too long
        desc = fb.get("description", "")
        if len(desc) > 60:
            desc = desc[:57] + "..."
        
        lines.append(
            f"{emoji} `{fb.get('id')}` — *{type_label}*\n"
            f"   {status_emoji} {status_label} | 👤 {fb.get('author_name', '?')}\n"
            f"   📝 {desc}\n"
        )
    
    lines.append(f"\n_Total: {len(feedbacks)} feedback(s)_")
    return "\n".join(lines)


@dp.message(Command("besoin"))
async def cmd_besoin(message: types.Message):
    """Commande /besoin — Signaler un besoin (MP uniquement)."""
    # Vérifie que c'est en MP (chat_type = private)
    if message.chat.type != "private":
        await message.answer(
            "⚠️ Cette commande fonctionne uniquement en **message privé**.\n\n"
            "Envoyez-moi un MP directement pour utiliser cette fonction."
        )
        return
    
    description = message.text.replace("/besoin", "").strip()
    
    if not description:
        await message.answer(FEEDBACK_MESSAGES["no_description"], parse_mode=ParseMode.MARKDOWN)
        return
    
    if len(description) < 3:
        await message.answer(FEEDBACK_MESSAGES["too_short"], parse_mode=ParseMode.MARKDOWN)
        return
    
    author = str(message.from_user.id)
    author_name = _get_author_name(message.from_user)
    
    fb = add_feedback("besoin", description, author, author_name)
    
    if fb:
        await message.answer(
            FEEDBACK_MESSAGES["besoin_added"].format(id=fb["id"]),
            parse_mode=ParseMode.MARKDOWN
        )
    else:
        await message.answer("❌ Erreur lors de l'enregistrement. Réessayez plus tard.")


@dp.message(Command("idee"))
async def cmd_idee(message: types.Message):
    """Commande /idee — Proposer une idée (MP uniquement)."""
    if message.chat.type != "private":
        await message.answer(
            "⚠️ Cette commande fonctionne uniquement en **message privé**.\n\n"
            "Envoyez-moi un MP directement pour utiliser cette fonction."
        )
        return
    
    description = message.text.replace("/idee", "").strip()
    
    if not description:
        await message.answer(FEEDBACK_MESSAGES["no_description"], parse_mode=ParseMode.MARKDOWN)
        return
    
    if len(description) < 3:
        await message.answer(FEEDBACK_MESSAGES["too_short"], parse_mode=ParseMode.MARKDOWN)
        return
    
    author = str(message.from_user.id)
    author_name = _get_author_name(message.from_user)
    
    fb = add_feedback("idee", description, author, author_name)
    
    if fb:
        await message.answer(
            FEEDBACK_MESSAGES["idee_added"].format(id=fb["id"]),
            parse_mode=ParseMode.MARKDOWN
        )
    else:
        await message.answer("❌ Erreur lors de l'enregistrement. Réessayez plus tard.")


@dp.message(Command("bug"))
async def cmd_bug(message: types.Message):
    """Commande /bug — Signaler un bug (MP uniquement)."""
    if message.chat.type != "private":
        await message.answer(
            "⚠️ Cette commande fonctionne uniquement en **message privé**.\n\n"
            "Envoyez-moi un MP directement pour utiliser cette fonction."
        )
        return
    
    description = message.text.replace("/bug", "").strip()
    
    if not description:
        await message.answer(FEEDBACK_MESSAGES["no_description"], parse_mode=ParseMode.MARKDOWN)
        return
    
    if len(description) < 3:
        await message.answer(FEEDBACK_MESSAGES["too_short"], parse_mode=ParseMode.MARKDOWN)
        return
    
    author = str(message.from_user.id)
    author_name = _get_author_name(message.from_user)
    
    fb = add_feedback("bug", description, author, author_name)
    
    if fb:
        await message.answer(
            FEEDBACK_MESSAGES["bug_added"].format(id=fb["id"]),
            parse_mode=ParseMode.MARKDOWN
        )
    else:
        await message.answer("❌ Erreur lors de l'enregistrement. Réessayez plus tard.")


@dp.message(Command("liste"))
async def cmd_liste(message: types.Message):
    """Commande /liste — Lister les feedbacks (MP uniquement)."""
    if message.chat.type != "private":
        await message.answer(
            "⚠️ Cette commande fonctionne uniquement en **message privé**.\n\n"
            "Envoyez-moi un MP directement pour utiliser cette fonction."
        )
        return
    
    # Parse arguments: /liste [type] [statut]
    args = message.text.replace("/liste", "").strip().split()
    
    feedback_type = None
    status = None
    
    if args:
        if args[0] in ["besoin", "idee", "bug"]:
            feedback_type = args[0]
        if len(args) > 1 and args[1] in ["new", "vu", "en_cours", "fait"]:
            status = args[1]
    
    feedbacks = list_feedback(feedback_type, status, limit=10)
    
    text = _format_feedback_list(feedbacks, feedback_type)
    
    await message.answer(text, parse_mode=ParseMode.MARKDOWN)


@dp.message(Command("status"))
async def cmd_status(message: types.Message):
    """Commande /status — Changer le statut d'un feedback (admin uniquement, MP)."""
    if message.chat.type != "private":
        await message.answer(
            "⚠️ Cette commande fonctionne uniquement en **message privé**.\n\n"
            "Envoyez-moi un MP directement pour utiliser cette fonction."
        )
        return
    
    # Vérifie admin
    username = message.from_user.username
    user_id = str(message.from_user.id)
    
    is_admin = (
        username in FEEDBACK_ADMINS or
        user_id in FEEDBACK_ADMINS or
        username in ["paolo_wash", "littllmind"] or  # Fallback hardcoded
        user_id in ["paolo_wash", "littllmind"]
    )
    
    if not is_admin:
        await message.answer(FEEDBACK_MESSAGES["status_no_admin"], parse_mode=ParseMode.MARKDOWN)
        return
    
    # Parse arguments: /status <id> <status>
    args = message.text.replace("/status", "").strip().split()
    
    if len(args) < 2:
        await message.answer(FEEDBACK_MESSAGES["status_usage"], parse_mode=ParseMode.MARKDOWN)
        return
    
    feedback_id = args[0]
    new_status = args[1]
    
    if new_status not in ["new", "vu", "en_cours", "fait"]:
        await message.answer(FEEDBACK_MESSAGES["status_usage"], parse_mode=ParseMode.MARKDOWN)
        return
    
    success = update_status(feedback_id, new_status)
    
    if success:
        await message.answer(
            FEEDBACK_MESSAGES["status_updated"].format(id=feedback_id, status=STATUS_LABELS.get(new_status, new_status)),
            parse_mode=ParseMode.MARKDOWN
        )
    else:
        await message.answer(
            FEEDBACK_MESSAGES["status_not_found"].format(id=feedback_id),
            parse_mode=ParseMode.MARKDOWN
        )


# ============ NOTIFICATIONS GROUPE ============

async def notify_new_reservation(client_name: str, service: str, date: str, time: str):
    """
    Envoie une notification au groupe Telegram lors d'une nouvelle réservation.
    
    Args:
        client_name: Nom du client
        service: Service réservé
        date: Date du rendez-vous
        time: Heure du rendez-vous
    """
    text = (
        "🔔 **Nouvelle réservation !**\n\n"
        f"👤 Client : {client_name}\n"
        f"🚗 Service : {service}\n"
        f"📅 Date : {date}\n"
        f"⏰ Heure : {time}\n\n"
        "Voir les détails sur SimplyBook.me"
    )
    
    try:
        await bot.send_message(
            chat_id=GROUP_ID,
            text=text,
            parse_mode=ParseMode.MARKDOWN
        )
        logger.info(f"Notification envoyée au groupe {GROUP_ID}")
    except Exception as e:
        logger.error(f"Erreur envoi notification groupe: {e}")


# ============ GESTION ERREURS ============

@dp.error()
async def error_handler(event, data):
    """Gestionnaire global d'erreurs."""
    logger.error(f"Erreur survenue: {event.exception}", exc_info=True)


# ============ MAIN ============

async def main():
    """Point d'entrée principal du bot."""
    logger.info("🚀 Démarrage du bot BOTCH - Paolo-Wash")
    
    # Supprimer webhook existant et démarrer polling
    await bot.delete_webhook(drop_pending_updates=True)
    
    # Démarrer le polling
    await dp.start_polling(bot)


if __name__ == "__main__":
    try:
        asyncio.run(main())
    except (KeyboardInterrupt, SystemExit):
        logger.info("🛑 Arrêt du bot")
    except Exception as e:
        logger.error(f"Erreur fatale: {e}", exc_info=True)
