<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gestionnaire_de_flotte_id');
            $table->unsignedBigInteger('vehicule_id');
            $table->unsignedBigInteger('chauffeur_id')->nullable();
            $table->string('type_assistance', 100);
            $table->string('titre', 160);
            $table->text('description')->nullable();
            $table->string('lieu', 190)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('niveau_urgence', ['faible', 'moyen', 'eleve', 'critique'])->default('moyen');
            $table->unsignedBigInteger('prestataire_id')->nullable();
            $table->string('prestataire_nom', 160)->nullable();
            $table->dateTime('date_demande')->nullable();
            $table->dateTime('date_intervention')->nullable();
            $table->dateTime('date_cloture')->nullable();
            $table->enum('statut', ['nouvelle', 'affectee', 'en_cours', 'resolue', 'annulee'])->default('nouvelle');
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->index('gestionnaire_de_flotte_id');
            $table->index('vehicule_id');
            $table->index('chauffeur_id');
            $table->index(['gestionnaire_de_flotte_id', 'statut']);
            $table->index('niveau_urgence');
            $table->index('date_demande');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistances');
    }
};
