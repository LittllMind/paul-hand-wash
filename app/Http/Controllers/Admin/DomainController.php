<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    /**
     * Affiche la liste des domaines
     */
    public function index()
    {
        $domaines = Domain::orderBy('id', 'desc')->paginate(10);
        return view('admin.domaines.index', compact('domaines'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('admin.domaines.create');
    }

    /**
     * Enregistre un nouveau domaine
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:domains,slug',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active', true);

        Domain::create($validated);

        return redirect()->route('admin.domaines.index')
            ->with('success', 'Domaine créé avec succès');
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Domain $domaine)
    {
        return view('admin.domaines.edit', compact('domaine'));
    }

    /**
     * Met à jour un domaine
     */
    public function update(Request $request, Domain $domaine)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:domains,slug,' . $domaine->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active', false);

        $domaine->update($validated);

        return redirect()->route('admin.domaines.index')
            ->with('success', 'Domaine mis à jour avec succès');
    }

    /**
     * Supprime un domaine
     */
    public function destroy(Domain $domaine)
    {
        $domaine->delete();

        return redirect()->route('admin.domaines.index')
            ->with('success', 'Domaine supprimé avec succès');
    }
}
