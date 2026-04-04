<?php

namespace Database\Factories;

use App\Models\Presence;
use App\Models\Lieu;
use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Presence>
 */
class PresenceFactory extends Factory
{
    protected $model = Presence::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lieu_id' => Lieu::factory(),
            'date' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'heure_debut' => '09:00',
            'heure_fin' => '18:00',
            'est_reserve' => false,
        ];
    }
}
