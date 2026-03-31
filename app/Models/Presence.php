<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($presence) {
            $validator = Validator::make($presence->getAttributes(), [
                'heure_debut' => 'required',
                'heure_fin' => 'required|after:heure_debut',
            ], [
                'heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }

    public function lieu()
    {
        return $this->belongsTo(Lieu::class);
    }
}
