<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rdv_concessionnaires', function (Blueprint $table) {
            $table->id();
            $table->string('jour');
            $table->datetime('heure');
            $table->unsignedBigInteger('gestionnaire_de_flotte_id');
            $table->unsignedBigInteger('concessionnaire_id');
            $table->timestamps();

            // Clés étrangères
            $table->foreign('gestionnaire_de_flotte_id')->references('id')->on('gestionnaire_de_flottes')->onDelete('cascade');
            $table->foreign('concessionnaire_id')->references('id')->on('userconcessionnaires')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rdv_concessionnaires');
    }
};
