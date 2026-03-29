<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lieu extends Model {
    protected $fillable = ['nom', 'adresse', 'latitude', 'longitude', 'actif'];
    
    public function presences() {
        return $this->hasMany(Presence::class);
    }
}
