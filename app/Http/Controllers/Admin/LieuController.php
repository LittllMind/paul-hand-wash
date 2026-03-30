<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lieu;
use Illuminate\Http\Request;

class LieuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lieux = Lieu::paginate(15);
        return view('admin.lieux.index', compact('lieux'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.lieux.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:100',
            'code_postal' => 'required|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $lieu = Lieu::create($validated);

        return redirect()->route('admin.lieux.index')
            ->with('success', 'Lieu créé avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lieu $lieu)
    {
        return view('admin.lieux.edit', compact('lieu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lieu $lieu)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:100',
            'code_postal' => 'required|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $lieu->update($validated);

        return redirect()->route('admin.lieux.index')
            ->with('success', 'Lieu mis à jour avec succès.');
    }
}
