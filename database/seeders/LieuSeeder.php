<?php

namespace Database\Seeders;

use App\Models\Lieu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LieuSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Lieu::create([
            'nom' => 'Parking Centre Commercial',
            'adresse' => '123 Rue de Paris',
            'ville' => 'Rozier',
            'code_postal' => '12345',
            'latitude' => 45.123,
            'longitude' => 5.678,
        ]);

        Lieu::create([
            'nom' => 'Parking Gare',
            'adresse' => '45 Avenue de la Gare',
            'ville' => 'Lyon',
            'code_postal' => '69000',
            'latitude' => 45.764,
            'longitude' => 4.835,
        ]);
    }
}
