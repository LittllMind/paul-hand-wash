<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Evenement;
use App\Models\Lieu;
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
        $dateDebut = $this->faker->dateTimeBetween('+1 week', '+1 month');
        $dateFin = (clone $dateDebut)->modify('+4 hours');

        return [
            'titre' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'lieu_id' => Lieu::factory(),
            'categorie_id' => null,
        ];
    }

    public function withCategorie(): static
    {
        return $this->state(fn (array $attributes) => [
            'categorie_id' => Categorie::factory(),
        ]);
    }
}
