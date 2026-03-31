<?php

namespace Tests\Unit\Models;

use App\Models\Lieu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class LieuTest extends TestCase
{
    /**
     * Tests structure du modèle sans base de données
     * (SQLite non disponible, testons la structure)
     */

    public function test_lieu_peut_etre_instancie_avec_donnees_valides()
    {
        // Test que le modèle accepte les attributs fillable correctement
        $lieu = new Lieu([
            'nom' => 'Parking Centre Commercial',
            'adresse' => '123 Rue de Paris',
            'ville' => 'Rozier',
            'code_postal' => '12345',
            'latitude' => 45.123,
            'longitude' => 5.678,
        ]);
        
        $this->assertEquals('Parking Centre Commercial', $lieu->nom);
        $this->assertEquals('Rozier', $lieu->ville);
        $this->assertEquals(45.123, $lieu->latitude);
        $this->assertEquals(5.678, $lieu->longitude);
    }

    public function test_lieu_requiert_nom_et_adresse_dans_fillable()
    {
        // Vérifie que nom et adresse sont dans fillable (peut être assigné en masse)
        $lieu = new Lieu();
        $reflection = new \ReflectionClass($lieu);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        $fillable = $property->getValue($lieu);
        
        $this->assertContains('nom', $fillable);
        $this->assertContains('adresse', $fillable);
    }

    public function test_lieu_a_tous_les_attributs_fillable()
    {
        $lieu = new Lieu();
        $reflection = new \ReflectionClass($lieu);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        $fillable = $property->getValue($lieu);

        $this->assertEquals([
            'nom',
            'adresse',
            'ville',
            'code_postal',
            'latitude',
            'longitude',
        ], $fillable);
    }

    public function test_lieu_a_bons_casts()
    {
        $lieu = new Lieu();
        $reflection = new \ReflectionClass($lieu);
        $property = $reflection->getProperty('casts');
        $property->setAccessible(true);
        $casts = $property->getValue($lieu);

        $this->assertEquals([
            'latitude' => 'float',
            'longitude' => 'float',
        ], $casts);
    }

    public function test_lieu_a_relation_presences()
    {
        $lieu = new Lieu();
        $this->assertTrue(method_exists($lieu, 'presences'));
    }

    public function test_lieu_etend_model()
    {
        // Vérification que la classe Lieu existe et étend Model
        $this->assertInstanceOf(Model::class, new Lieu());
    }

    public function test_lieu_utilise_has_factory()
    {
        // Vérification qu'elle utilise HasFactory
        $uses = class_uses(Lieu::class);
        $this->assertContains(HasFactory::class, $uses);
    }

    public function test_lieu_factory_existe()
    {
        // Vérification que la factory existe
        $this->assertTrue(class_exists(\Database\Factories\LieuFactory::class));
    }

    public function test_migration_lieu_existe()
    {
        // Vérification que la migration existe
        $migrationPath = database_path('migrations/2026_03_29_000001_create_lieux_table.php');
        $this->assertFileExists($migrationPath);
        
        // Vérification du contenu basique
        $content = file_get_contents($migrationPath);
        $this->assertStringContainsString('create', $content);
        $this->assertStringContainsString('lieux', $content);
    }

    public function test_table_lieu_existe_dans_bdd()
    {
        // Test qui sera rouge tant que migrate n'est pas fait
        // Nécessite SQLite - marqué comme skipped si pas disponible
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('SQLite extension not available');
        }

        $this->artisan('migrate:fresh');
        $this->assertTrue(\Schema::hasTable('lieux'));
    }

    public function test_lieu_peut_etre_cree_en_base()
    {
        // Test qui sera rouge tant que migrate n'est pas fait
        // Nécessite SQLite - marqué comme skipped si pas disponible
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('SQLite extension not available');
        }

        $this->artisan('migrate:fresh');
        
        $lieu = Lieu::create([
            'nom' => 'Test Parking',
            'adresse' => '123 Test Street',
            'ville' => 'Testville',
            'code_postal' => '12345',
        ]);

        $this->assertDatabaseHas('lieux', ['nom' => 'Test Parking']);
    }
}
