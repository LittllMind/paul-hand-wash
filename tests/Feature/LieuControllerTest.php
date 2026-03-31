<?php

namespace Tests\Feature;

use App\Models\Lieu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LieuControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_the_create_form(): void
    {
        $response = $this->get(route('lieux.create'));

        $response->assertStatus(200);
        $response->assertViewIs('lieux.create');
    }

    /** @test */
    public function it_can_store_a_new_lieu(): void
    {
        $data = [
            'nom' => 'Lieu Test',
            'adresse' => '123 Rue Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'France',
        ];

        $response = $this->post(route('lieux.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('lieux', $data);
    }

    /** @test */
    public function it_requires_a_nom_to_store_lieu(): void
    {
        $data = [
            'nom' => '',
            'adresse' => '123 Rue Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'France',
        ];

        $response = $this->post(route('lieux.store'), $data);

        $response->assertSessionHasErrors('nom');
    }

    /** @test */
    public function it_requires_an_adresse_to_store_lieu(): void
    {
        $data = [
            'nom' => 'Lieu Test',
            'adresse' => '',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'France',
        ];

        $response = $this->post(route('lieux.store'), $data);

        $response->assertSessionHasErrors('adresse');
    }

    /** @test */
    public function it_requires_a_code_postal_to_store_lieu(): void
    {
        $data = [
            'nom' => 'Lieu Test',
            'adresse' => '123 Rue Test',
            'code_postal' => '',
            'ville' => 'Paris',
            'pays' => 'France',
        ];

        $response = $this->post(route('lieux.store'), $data);

        $response->assertSessionHasErrors('code_postal');
    }

    /** @test */
    public function it_requires_a_ville_to_store_lieu(): void
    {
        $data = [
            'nom' => 'Lieu Test',
            'adresse' => '123 Rue Test',
            'code_postal' => '75000',
            'ville' => '',
            'pays' => 'France',
        ];

        $response = $this->post(route('lieux.store'), $data);

        $response->assertSessionHasErrors('ville');
    }

    /** @test */
    public function it_requires_a_pays_to_store_lieu(): void
    {
        $data = [
            'nom' => 'Lieu Test',
            'adresse' => '123 Rue Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => '',
        ];

        $response = $this->post(route('lieux.store'), $data);

        $response->assertSessionHasErrors('pays');
    }

    /** @test */
    public function it_redirects_to_show_page_after_storing(): void
    {
        $data = [
            'nom' => 'Lieu Test',
            'adresse' => '123 Rue Test',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'France',
        ];

        $response = $this->post(route('lieux.store'), $data);

        $lieu = Lieu::first();
        $response->assertRedirect(route('lieux.show', $lieu));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_can_display_a_lieu(): void
    {
        $lieu = Lieu::factory()->create();

        $response = $this->get(route('lieux.show', $lieu));

        $response->assertStatus(200);
        $response->assertViewIs('lieux.show');
        $response->assertViewHas('lieu', $lieu);
    }
}
