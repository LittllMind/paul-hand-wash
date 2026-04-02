<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Categorie extends Model
{
    /** @use HasFactory<\Database\Factories\CategorieFactory> */
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'couleur',
    ];

    public function evenements(): HasMany
    {
        return $this->hasMany(Evenement::class);
    }

    public function lieux(): BelongsToMany
    {
        return $this->belongsToMany(Lieu::class, 'categorie_lieu');
    }
}
