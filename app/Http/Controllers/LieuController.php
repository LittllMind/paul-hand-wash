<?php

namespace App\Http\Controllers;

use App\Models\Lieu;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LieuController extends Controller
{
    /**
     * Show the form for creating a new lieu.
     */
    public function create(): View
    {
        return view('lieux.create');
    }

    /**
     * Store a newly created lieu in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'code_postal' => 'required|string|max:20',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
        ]);

        $lieu = Lieu::create($validated);

        return redirect()->route('lieux.show', $lieu)
            ->with('success', 'Lieu créé avec succès.');
    }

    /**
     * Display the specified lieu.
     */
    public function show(Lieu $lieu): View
    {
        return view('lieux.show', compact('lieu'));
    }
}
