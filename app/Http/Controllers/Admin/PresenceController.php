<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lieu;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    /**
     * Display the presences calendar.
     */
    public function index(Request $request)
    {
        $moisParam = $request->input('mois', now()->format('Y-m'));
        $debut = Carbon::parse($moisParam . '-01')->startOfMonth();
        $fin = $debut->copy()->endOfMonth();

        $lieux = Lieu::all();
        
        $query = Presence::with('lieu')
            ->whereBetween('date', [$debut, $fin]);

        if ($request->filled('lieu_id')) {
            $query->where('lieu_id', $request->input('lieu_id'));
        }

        $presences = $query->orderBy('date')
            ->orderBy('heure_debut')
            ->get();

        // Construire le calendrier
        $jours = [];
        $jourCourant = $debut->copy()->startOfWeek(Carbon::MONDAY);
        
        while ($jourCourant <= $fin->copy()->endOfWeek(Carbon::SUNDAY)) {
            $creneauxDuJour = $presences->filter(fn ($p) => $p->date->format('Y-m-d') === $jourCourant->format('Y-m-d'));
            
            $jours[] = [
                'date' => $jourCourant->copy(),
                'creneaux' => $creneauxDuJour,
            ];
            
            $jourCourant->addDay();
        }

        return view('admin.presences.calendrier', [
            'jours' => $jours,
            'lieux' => $lieux,
            'mois' => $moisParam,
            'titreMois' => $debut->translatedFormat('F Y'),
        ]);
    }

    /**
     * Show the form for creating batch presences.
     */
    public function createBatch()
    {
        $lieux = Lieu::all();
        return view('admin.presences.batch-create', compact('lieux'));
    }

    /**
     * Store batch presences.
     */
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'lieu_id' => 'required|exists:lieux,id',
            'dates' => 'required|array',
            'dates.*' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        $count = 0;
        foreach ($validated['dates'] as $date) {
            Presence::create([
                'lieu_id' => $validated['lieu_id'],
                'date' => $date,
                'heure_debut' => $validated['heure_debut'],
                'heure_fin' => $validated['heure_fin'],
                'est_reserve' => false,
            ]);
            $count++;
        }

        return redirect()->route('admin.presences.index')
            ->with('success', "$count créneaux créés avec succès.");
    }
}
