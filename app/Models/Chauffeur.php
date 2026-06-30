<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    use HasFactory;

    protected $table = 'chauffeurs';

    public function fonction()
    {
        return $this->belongsTo(Fonction::class, 'fonction_id');
    }
    
    public function ville()
    {
        return $this->belongsTo(Ville::class, 'ville_id');
    }

    public function vehicules()
    {
        return $this->hasMany(Vehicule::class, 'user_id');
    }

    public function gestionnaire_de_flotte()
    {
        return $this->belongsTo(GestionnaireDeFlotte::class, 'gestionnaire_de_flotte_id');
    }

}
