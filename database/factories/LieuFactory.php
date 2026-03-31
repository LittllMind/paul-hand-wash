<?php

namespace Database\Factories;

use App\Models\Lieu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lieu>
 */
class LieuFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var string
     */
    protected $model = Lieu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->company() . ' - ' . fake()->streetName(),
            'adresse' => fake()->streetAddress(),
            'ville' => fake()->city(),
            'code_postal' => fake()->postcode(),
            'latitude' => fake()->optional(0.7)->latitude(42, 47),
            'longitude' => fake()->optional(0.7)->longitude(-5, 8),
        ];
    }

    /**
     * Indiquer que le lieu a des coordonnées GPS.
     */
    public function withCoordinates(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => fake()->latitude(42, 47),
            'longitude' => fake()->longitude(-5, 8),
        ]);
    }
}
