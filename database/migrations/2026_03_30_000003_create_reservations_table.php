<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presence_id')->constrained();
            $table->string('client_nom', 100);
            $table->string('client_telephone', 20);
            $table->string('client_email', 100);
            $table->string('prestation', 50); // Express, Essentiel, Premium
            $table->decimal('montant', 8, 2);
            $table->boolean('paye')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('reservations');
    }
};
