<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reparation extends Model
{
    protected $table = 'reparations';

    protected $fillable = [
        'gestionnaire_de_flotte_id',
        'vehicule_id',
        'chauffeur_id',
        'prestataire_id',
        'prestataire_nom',
        'assistance_id',
        'titre',
        'description_panne',
        'diagnostic',
        'proforma_reference',
        'proforma_montant',
        'validation_financiere',
        'date_entree',
        'date_sortie_prevue',
        'date_sortie',
        'cout_final',
        'statut',
        'commentaire',
    ];

    public function gestionnaire_de_flotte()
    {
        return $this->belongsTo(GestionnaireDeFlotte::class, 'gestionnaire_de_flotte_id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class, 'chauffeur_id');
    }

    public function prestataire()
    {
        return $this->belongsTo(Garage_flotte::class, 'prestataire_id');
    }

    public function assistance()
    {
        return $this->belongsTo(Assistance::class, 'assistance_id');
    }
}
