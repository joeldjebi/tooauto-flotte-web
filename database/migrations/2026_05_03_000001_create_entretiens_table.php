<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entretiens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gestionnaire_de_flotte_id');
            $table->unsignedBigInteger('vehicule_id');
            $table->unsignedBigInteger('chauffeur_id')->nullable();
            $table->string('type_entretien', 100);
            $table->string('titre', 160);
            $table->text('description')->nullable();
            $table->date('date_prevue')->nullable();
            $table->date('date_realisation')->nullable();
            $table->unsignedInteger('kilometrage')->nullable();
            $table->decimal('cout', 12, 2)->nullable();
            $table->string('prestataire', 160)->nullable();
            $table->enum('statut', ['planifie', 'en_cours', 'realise', 'annule'])->default('planifie');
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->index('gestionnaire_de_flotte_id');
            $table->index('vehicule_id');
            $table->index('chauffeur_id');
            $table->index(['gestionnaire_de_flotte_id', 'statut']);
            $table->index('date_prevue');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entretiens');
    }
};
