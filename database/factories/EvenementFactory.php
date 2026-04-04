<?php

namespace Database\Factories;

use App\Models\Evenement;
use App\Models\Lieu;
use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evenement>
 */
class EvenementFactory extends Factory
{
    protected $model = Evenement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'date_debut' => $this->faker->dateTimeBetween('now', '+1 month'),
            'date_fin' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'lieu_id' => Lieu::factory(),
            'categorie_id' => Categorie::factory(),
            'places_limite' => null,
        ];
    }

    public function withPlacesLimit(int $places): self
    {
        return $this->state(fn (array $attributes) => [
            'places_limite' => $places,
        ]);
    }
}
