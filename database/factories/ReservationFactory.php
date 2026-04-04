<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Presence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = \App\Models\Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'presence_id' => Presence::factory(),
            'client_nom' => $this->faker->name(),
            'client_telephone' => $this->faker->phoneNumber(),
            'client_email' => $this->faker->safeEmail(),
            'prestation' => 'Lavage Premium',
            'montant' => $this->faker->randomFloat(2, 20, 150),
            'paye' => false,
        ];
    }

    /**
     * Configurer la factory avec une présence spécifique.
     */
    public function withPresence(?\App\Models\Presence $presence = null): static
    {
        return $this->state(fn (array $attributes) => [
            'presence_id' => $presence ? $presence->id : Presence::factory(),
        ]);
    }
}
