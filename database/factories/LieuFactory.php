<?php

namespace Database\Factories;

use App\Models\Lieu;
use Illuminate\Database\Eloquent\Factories\Factory;

class LieuFactory extends Factory
{
    protected $model = Lieu::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->company() . ' Parking',
            'adresse' => fake()->streetAddress(),
            'ville' => fake()->city(),
            'code_postal' => fake()->postcode(),
            'latitude' => fake()->latitude(43, 49),
            'longitude' => fake()->longitude(-1, 7),
        ];
    }
}
