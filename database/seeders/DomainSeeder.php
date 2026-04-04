<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Domain;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domaines = [
            [
                'slug' => 'savon-enfant',
                'name' => 'Savon pour Enfant',
                'description' => 'Savons doux et naturels pour les enfants',
                'image' => 'images/domaines/savon-enfant.jpg',
                'active' => true,
            ],
            [
                'slug' => 'boutique-zero-dechet',
                'name' => 'Boutique Zéro Déchet',
                'description' => 'Produits écologiques sans emballage',
                'image' => 'images/domaines/zero-dechet.jpg',
                'active' => true,
            ],
            [
                'slug' => 'cosmetique-naturelle',
                'name' => 'Cosmétique Naturelle',
                'description' => 'Produits de beauté bio et naturels',
                'image' => 'images/domaines/cosmetique.jpg',
                'active' => true,
            ],
            [
                'slug' => 'atelier-diy',
                'name' => 'Atelier DIY',
                'description' => 'Fabriquez vos propres produits',
                'image' => 'images/domaines/diy.jpg',
                'active' => true,
            ],
        ];

        foreach ($domaines as $domaine) {
            Domain::firstOrCreate(
                ['slug' => $domaine['slug']],
                $domaine
            );
        }
    }
}
