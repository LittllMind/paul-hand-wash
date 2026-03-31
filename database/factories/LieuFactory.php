<?php

namespace Database\Factories;

use App\Models\Lieu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lieu>
 */
class LieuFactory extends Factory
{
    protected $model = Lieu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->company(),
            'adresse' => $this->faker->streetAddress(),
            'ville' => $this->faker->city(),
            'code_postal' => $this->faker->postcode(),
            'pays' => $this->faker->country(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];
    }
}
