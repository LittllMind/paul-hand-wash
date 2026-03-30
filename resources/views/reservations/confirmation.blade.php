<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation confirmée - Paul Hand Wash</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; text-align: center; }
        h1 { color: #4CAF50; }
        .success-icon { font-size: 4em; margin: 20px 0; }
        .reservation-card { background: #e8f5e9; border: 2px solid #4CAF50; padding: 30px; border-radius: 12px; margin: 30px 0; text-align: left; }
        .reservation-card h2 { color: #2e7d32; margin-top: 0; }
        .detail { margin: 15px 0; padding: 10px; background: white; border-radius: 6px; }
        .detail-label { font-weight: bold; color: #666; font-size: 0.9em; }
        .detail-value { font-size: 1.1em; color: #333; }
        .price { font-size: 1.5em; color: #4CAF50; font-weight: bold; }
        .btn { display: inline-block; background: #2196F3; color: white; padding: 15px 30px; text-decoration: none; border-radius: 6px; margin: 10px; }
        .btn:hover { background: #1976D2; }
        .info-box { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="success-icon">✅</div>
    <h1>Réservation confirmée !</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <div class="reservation-card">
        <h2>📋 Récapitulatif de votre réservation</h2>

        <div class="detail">
            <div class="detail-label">Numéro de réservation</div>
            <div class="detail-value">#{{ $reservation->id }}</div>
        </div>

        <div class="detail">
            <div class="detail-label">Prestation</div>
            <div class="detail-value">{{ $reservation->prestation }}</div>
        </div>

        <div class="detail">
            <div class="detail-label">Lieu</div>
            <div class="detail-value">{{ $reservation->presence->lieu->nom }}</div>
        </div>

        <div class="detail">
            <div class="detail-label">Date et heure</div>
            <div class="detail-value">
                {{ $reservation->presence->date->translatedFormat('l d F Y') }}<br>
                {{ $reservation->presence->heure_debut->format('H:i') }} - {{ $reservation->presence->heure_fin->format('H:i') }}
            </div>
        </div>

        <div class="detail">
            <div class="detail-label">Client</div>
            <div class="detail-value">{{ $reservation->client_nom }}</div>
        </div>

        <div class="detail">
            <div class="detail-label">Montant total</div>
            <div class="price">{{ $reservation->montant }}€</div>
        </div>

        <div class="detail">
            <div class="detail-label">Statut du paiement</div>
            <div class="detail-value">{{ $reservation->paye ? 'Payé' : 'À payer sur place' }}</div>
        </div>
    </div>

    <div class="info-box">
        📧 Un email de confirmation a été envoyé à <strong>{{ $reservation->client_email }}</strong><br>
        📱 Nous vous rappellerons au <strong>{{ $reservation->client_telephone }}</strong> pour confirmer.
    </div>

    <a href="{{ route('reserver') }}" class="btn">Nouvelle réservation</a>
    <a href="/" class="btn" style="background: #666;">Retour à l'accueil</a>

    <footer style="margin-top: 40px; color: #999;">
        <p>Paul Hand Wash - Merci de votre confiance !</p>
    </footer>
</body>
</html>
