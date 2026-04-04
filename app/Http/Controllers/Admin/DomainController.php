<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Services\Cache\DomainCacheService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DomainController extends Controller
{
    /**
     * Display a listing of domains.
     */
    public function index(): View
    {
        $domaines = Domain::all();
        return view('admin.domaines.index', compact('domaines'));
    }

    /**
     * Show the form for creating a new domain.
     */
    public function create(): View
    {
        return view('admin.domaines.create');
    }

    /**
     * Store a newly created domain.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:domains',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Domain::create($validated);

        // Clear cache after creating
        DomainCacheService::clear();

        return redirect()->route('admin.domaines.index')
            ->with('success', 'Domaine créé avec succès.');
    }

    /**
     * Show the form for editing a domain.
     */
    public function edit(Domain $domaine): View
    {
        return view('admin.domaines.edit', compact('domaine'));
    }

    /**
     * Update the specified domain.
     */
    public function update(Request $request, Domain $domaine): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:domains,slug,' . $domaine->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $domaine->update($validated);

        // Clear cache after updating
        DomainCacheService::clear();

        return redirect()->route('admin.domaines.index')
            ->with('success', 'Domaine mis à jour avec succès.');
    }

    /**
     * Remove the specified domain.
     */
    public function destroy(Domain $domaine): RedirectResponse
    {
        $domaine->delete();

        // Clear cache after deleting
        DomainCacheService::clear();

        return redirect()->route('admin.domaines.index')
            ->with('success', 'Domaine supprimé avec succès.');
    }
}
