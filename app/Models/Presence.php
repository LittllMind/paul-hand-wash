<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'lieu_id',
        'date',
        'heure_debut',
        'heure_fin',
        'est_reserve',
    ];

    protected $casts = [
        'date' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'est_reserve' => 'boolean',
    ];

    public function lieu()
    {
        return $this->belongsTo(Lieu::class);
    }
}
