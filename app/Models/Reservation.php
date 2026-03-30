<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'presence_id',
        'client_nom',
        'client_telephone',
        'client_email',
        'prestation',
        'montant',
        'paye',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'paye' => 'boolean',
    ];

    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }
}
