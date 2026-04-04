<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Domain;

class LandingPageDomainesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Je vois les domaines sur la landing page
     */
    public function test_je_vois_les_domaines_sur_la_landing(): void
    {
        // Arrange: Créer des domaines actifs
        $domain1 = Domain::create([
            'slug' => 'savon-enfant',
            'name' => 'Savon pour Enfant',
            'description' => 'Savons doux et naturels pour les enfants',
            'image' => 'images/domaines/savon-enfant.jpg',
            'active' => true,
        ]);

        $domain2 = Domain::create([
            'slug' => 'boutique-zero-dechet',
            'name' => 'Boutique Zéro Déchet',
            'description' => 'Produits écologiques sans emballage',
            'image' => 'images/domaines/zero-dechet.jpg',
            'active' => true,
        ]);

        // Inactif - ne doit pas apparaître
        Domain::create([
            'slug' => 'domaine-inactif',
            'name' => 'Domaine Inactif',
            'description' => 'Ne doit pas s\'afficher',
            'image' => 'images/inactif.jpg',
            'active' => false,
        ]);

        // Act: Visiter la landing page
        $response = $this->get('/');

        // Assert: La page se charge et affiche les domaines actifs
        $response->assertStatus(200);
        $response->assertSee('Savon pour Enfant');
        $response->assertSee('Boutique Zéro Déchet');
        $response->assertSee('savon-enfant');
        $response->assertSee('boutique-zero-dechet');
        $response->assertDontSee('Domaine Inactif');
    }

    /**
     * Test: Aucun domaine actif = message approprié
     */
    public function test_message_quand_aucun_domaine_actif(): void
    {
        // Arrange: Créer uniquement des domaines inactifs
        Domain::create([
            'slug' => 'inactif',
            'name' => 'Inactif',
            'description' => 'Test',
            'image' => 'test.jpg',
            'active' => false,
        ]);

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Aucun domaine disponible');
    }

    /**
     * Test: Les domaines sont affichés dans l'ordre (tri par id/ordre)
     */
    public function test_domaines_affiches_dans_ordre(): void
    {
        // Arrange - "deuxieme" créé en premier = ID inférieur
        $domainA = Domain::create([
            'slug' => 'deuxieme',
            'name' => 'Deuxième Domaine',
            'description' => 'Test',
            'image' => 'test.jpg',
            'active' => true,
        ]);

        $domainB = Domain::create([
            'slug' => 'premier',
            'name' => 'Premier Domaine',
            'description' => 'Test',
            'image' => 'test.jpg',
            'active' => true,
        ]);

        // Act
        $response = $this->get('/');

        // Assert: Vérifier que les deux domaines apparaissent
        $response->assertSee('Premier Domaine');
        $response->assertSee('Deuxième Domaine');
        
        // "Deuxième" (ID inférieur) doit apparaître avant "Premier" dans le HTML
        $content = $response->getContent();
        $posDeuxieme = strpos($content, 'Deuxième Domaine');
        $posPremier = strpos($content, 'Premier Domaine');
        
        $this->assertLessThan($posPremier, $posDeuxieme, 'Deuxième Domaine doit apparaître avant Premier Domaine');
    }
}
