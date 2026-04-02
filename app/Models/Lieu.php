<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lieu extends Model
{
    use HasFactory;

    protected $table = 'lieux';

    protected $fillable = [
        'nom',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Categorie::class, 'categorie_lieu');
    }
}