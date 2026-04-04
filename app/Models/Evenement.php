<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Evenement extends Model
{
    /** @use HasFactory\Database\Factories\EvenementFactory */
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'lieu_id',
        'categorie_id',
        'places_limite',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'places_limite' => 'integer',
    ];

    public function lieu(): BelongsTo
    {
        return $this->belongsTo(Lieu::class);
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('inscrit_le')
            ->withTimestamps();
    }

    public function inscriptionsCount(): int
    {
        return $this->users()->count();
    }

    public function hasAvailablePlaces(): bool
    {
        if (is_null($this->places_limite)) {
            return true;
        }
        return $this->inscriptionsCount() < $this->places_limite;
    }

    public function isUserInscribed(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
}
