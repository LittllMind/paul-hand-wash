<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Lieu;
use App\Models\Presence;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display available slots for reservation.
     */
    public function index(Request $request)
    {
        $lieux = Lieu::all();
        
        $query = Presence::with('lieu')
            ->where('est_reserve', false)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('heure_debut');

        if ($request->filled('lieu_id')) {
            $query->where('lieu_id', $request->input('lieu_id'));
        }

        if ($request->filled('date')) {
            $query->where('date', $request->input('date'));
        }

        $creneaux = $query->paginate(20);

        // Grouper par date pour affichage
        $creneauxParDate = $creneaux->groupBy(function ($creneau) {
            return $creneau->date->format('Y-m-d');
        });

        return view('reservations.choix-creneau', [
            'creneauxParDate' => $creneauxParDate,
            'lieux' => $lieux,
            'creneaux' => $creneaux,
        ]);
    }

    /**
     * Show the reservation form for a specific slot.
     */
    public function show(Presence $presence)
    {
        // Vérifier que le créneau est disponible
        if ($presence->est_reserve) {
            return redirect()->route('reserver')
                ->with('error', 'Ce créneau n\'est plus disponible.');
        }

        // Prestations disponibles
        $prestations = [
            'Express' => [
                'nom' => 'Lavage Express',
                'prix' => 15,
                'duree' => '20 min',
                'description' => 'Lavage extérieur rapide'
            ],
            'Essentiel' => [
                'nom' => 'Lavage Essentiel',
                'prix' => 25,
                'duree' => '45 min',
                'description' => 'Intérieur + extérieur'
            ],
            'Premium' => [
                'nom' => 'Lavage Premium',
                'prix' => 45,
                'duree' => '90 min',
                'description' => 'Lavage complet + cire + jantes'
            ],
        ];

        return view('reservations.formulaire', [
            'creneau' => $presence,
            'prestations' => $prestations,
        ]);
    }

    /**
     * Store a new reservation.
     */
    public function store(Request $request, Presence $presence)
    {
        // Vérifier que le créneau est encore disponible
        if ($presence->est_reserve) {
            return redirect()->route('reserver')
                ->with('error', 'Ce créneau n\'est plus disponible.');
        }

        // Validation
        $validated = $request->validate([
            'client_nom' => 'required|string|max:100',
            'client_telephone' => 'required|string|max:20',
            'client_email' => 'required|email|max:100',
            'prestation' => 'required|in:Express,Essentiel,Premium',
        ]);

        // Prix des prestations
        $prix = [
            'Express' => 15,
            'Essentiel' => 25,
            'Premium' => 45,
        ];

        // Créer la réservation
        $reservation = Reservation::create([
            'presence_id' => $presence->id,
            'client_nom' => $validated['client_nom'],
            'client_telephone' => $validated['client_telephone'],
            'client_email' => $validated['client_email'],
            'prestation' => $validated['prestation'],
            'montant' => $prix[$validated['prestation']],
            'paye' => false,
        ]);

        // Marquer le créneau comme réservé
        $presence->marquerReservee();

        return redirect()->route('reserver.confirmation', $reservation)
            ->with('success', 'Votre réservation a été confirmée !');
    }

    /**
     * Show the confirmation page for a reservation.
     */
    public function confirmation(Reservation $reservation)
    {
        $reservation->load('presence.lieu');

        return view('reservations.confirmation', [
            'reservation' => $reservation,
        ]);
    }
}
