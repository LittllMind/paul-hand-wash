<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Evenement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InscriptionConfirmation;

class InscriptionEvenementController extends Controller
{
    /**
     * Inscrire un utilisateur à un événement.
     */
    public function inscrire(Request $request, Evenement $evenement)
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour vous inscrire.');
        }

        // Vérifier si déjà inscrit
        if ($evenement->isUserInscribed($user)) {
            return redirect()->back()->with('error', 'Vous êtes déjà inscrit à cet événement.');
        }

        // Vérifier les places disponibles
        if (!$evenement->hasAvailablePlaces()) {
            return redirect()->back()->with('error', 'Il n\'y a plus de places disponibles pour cet événement.');
        }

        // Inscrire l'utilisateur
        $evenement->users()->attach($user);

        // Envoyer email de confirmation
        try {
            Mail::to($user->email)->send(new InscriptionConfirmation($evenement, $user));
        } catch (\Exception $e) {
            // Log l'erreur mais ne pas bloquer l'inscription
            \Log::error('Erreur envoi email inscription: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Vous êtes inscrit à l\'événement ! Un email de confirmation vous a été envoyé.');
    }

    /**
     * Désinscrire un utilisateur d'un événement.
     */
    public function desinscrire(Request $request, Evenement $evenement)
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Vérifier si inscrit
        if (!$evenement->isUserInscribed($user)) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas inscrit à cet événement.');
        }

        // Désinscrire l'utilisateur
        $evenement->users()->detach($user);

        return redirect()->back()->with('success', 'Vous êtes désinscrit de l\'événement.');
    }
}
