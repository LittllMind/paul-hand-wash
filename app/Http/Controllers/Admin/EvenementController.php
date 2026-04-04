<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evenement;
use App\Models\Lieu;
use App\Models\Categorie;
use App\Http\Requests\StoreEvenementRequest;
use App\Http\Requests\UpdateEvenementRequest;

class EvenementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $evenements = Evenement::with(['lieu', 'categorie'])->paginate(15);
        return view('admin.evenements.index', compact('evenements'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Evenement $evenement)
    {
        $evenement->load(['lieu', 'categorie']);
        return view('admin.evenements.show', compact('evenement'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lieux = Lieu::all();
        $categories = Categorie::all();
        return view('admin.evenements.create', compact('lieux', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEvenementRequest $request)
    {
        $evenement = Evenement::create($request->validated());

        return redirect()->route('admin.evenements.index')
            ->with('success', 'Événement créé avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Evenement $evenement)
    {
        $lieux = Lieu::all();
        $categories = Categorie::all();
        return view('admin.evenements.edit', compact('evenement', 'lieux', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEvenementRequest $request, Evenement $evenement)
    {
        $evenement->update($request->validated());

        return redirect()->route('admin.evenements.index')
            ->with('success', 'Événement mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evenement $evenement)
    {
        $evenement->delete();

        return redirect()->route('admin.evenements.index')
            ->with('success', 'Événement supprimé avec succès.');
    }
}
