<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lieu;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Presence>
 */
class PresenceFactory extends Factory
{
    protected $model = \App\Models\Presence::class;

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
            'heure_fin' => '10:00',
            'est_reserve' => false,
        ];
    }
}
