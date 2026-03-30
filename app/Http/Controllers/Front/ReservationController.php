<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Lieu;
use App\Models\Presence;
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
}
