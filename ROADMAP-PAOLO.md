# ROADMAP PAOLO - Application Lavage Auto

## 🎯 Vision

Application Laravel pour **Paolo Wash** : gestion de lavage automobile à domicile.

**Acteurs:**
- **Paolo** (prestataire) : gère ses créneaux, lieux, réservations
- **Clients** : réservent créneaux en ligne

---

## ✅ État Actuel (Audit 2026-03-29)

| Composant | Statut | Détail |
|-----------|--------|--------|
| Models (Lieu, Presence, Reservation) | ✅ | Structure de base présente |
| Migrations | ✅ | Tables créées |
| PresenceController | ⚠️ | Partiel (dashboard seul) |
| ReservationController | ⚠️ | Partiel (manque CRUD) |
| Vues admin | ⚠️ | presences.blade.php existante |
| Routes | ❌ | routes/paolo.php minimal |
| Tests | ❌ | Aucun test écrit |

---

## 📋 Backlog TDD

### **T1: CRUD Lieux** [SIMPLE]
**Objectif**: Gérer les points de rendez-vous (adresses, GPS)

**Test**:
```php
public function test_admin_peut_creer_lieu()
{
    $response = $this->post('/admin/lieux', [
        'nom' => 'Parking Centre Commercial',
        'adresse' => '123 Rue de Paris',
        'ville' => 'Rozier',
        'code_postal' => '12345',
        'latitude' => 45.123,
        'longitude' => 5.678,
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('lieux', ['nom' => 'Parking Centre Commercial']);
}
```

**Implémentation**:
- Admin/LieuController (CRUD)
- Vues Blade : index, create, edit
- Routes admin/lieux

**Validation**: Tests verts + navigation fonctionnelle

---

### **T2: Gestion Présences Calendrier** [MOYENNE]
**Objectif**: Paolo déclare ses disponibilités

**Test**:
```php
public function test_paolo_peut_creer_presence()
{
    $lieu = Lieu::factory()->create();
    $response = $this->post('/admin/presences', [
        'lieu_id' => $lieu->id,
        'date' => '2026-04-01',
        'heure_debut' => '09:00',
        'heure_fin' => '17:00',
        'est_reserve' => false,
    ]);
    $response->assertStatus(201);
}
```

**Implémentation**:
- Admin/PresenceController (CRUD complet)
- Vue calendrier (intégration FullCalendar ou similaire)
- Filtrage par date

**Validation**: Calendrier interactif fonctionnel

---

### **T3: Réservation Client** [MOYENNE]
**Objectif**: Client choisit créneau et réserve

**Test**:
```php
public function test_client_peut_reserver_creneau()
{
    $presence = Presence::factory()->create(['est_reserve' => false]);
    $response = $this->post('/reservations', [
        'presence_id' => $presence->id,
        'client_nom' => 'Dupont',
        'client_telephone' => '0600000000',
        'client_email' => 'dupont@example.com',
        'prestation' => 'Essentiel',
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('reservations', ['client_nom' => 'Dupont']);
    $this->assertDatabaseHas('presences', ['id' => $presence->id, 'est_reserve' => true]);
}
```

**Implémentation**:
- ReservationController (front)
- Formulaire réservation avec choix créneau
- Marquage présence comme réservée

**Validation**: Flux complet de réservation

---

### **T4: Notifications** [COMPLEXE]
**Objectif**: Email/SMS confirmation réservation

**Features**:
- Email confirmation client
- Email rappel J-1
- SMS optionnel (Twilio)

---

### **T5: Paiement** [COMPLEXE]
**Objectif**: Paiement en ligne (Stripe)

**Features**:
- Intégration Stripe
- Paiement à la réservation ou sur place
- Facturation automatique

---

## 🎯 Priorité

| Rang | Tâche | Complexité | Valeur |
|------|-------|------------|--------|
| 1 | T1: CRUD Lieux | Simple | ⭐⭐⭐ |
| 2 | T2: Calendrier Présences | Moyenne | ⭐⭐⭐ |
| 3 | T3: Réservation Client | Moyenne | ⭐⭐⭐ |
| 4 | T4: Notifications | Complexe | ⭐⭐ |
| 5 | T5: Paiement | Complexe | ⭐⭐ |

---

## 📁 Organisation Git

```
feature/T1.1-crud-lieux
feature/T1.2-tests-lieux
feature/T2.1-calendrier-presences
feature/T3.1-reservation-frontend
...
```

---

## 📝 Notes

- Chaque tâche = une branche = tests d'abord
- Pas de référence au projet vinyles/bougies originel
- Paolo doit pouvoir utiliser l'admin sans formation

---
*Créé: 2026-03-29*
*Prochaine tâche: T1.1 - CRUD Lieux*
