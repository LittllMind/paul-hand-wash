<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lieux', function (Blueprint $table) {
            $table->string('pays', 100)->nullable()->after('code_postal');
        });
    }

    public function down(): void
    {
        Schema::table('lieux', function (Blueprint $table) {
            $table->dropColumn('pays');
        });
    }
};
