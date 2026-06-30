<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicule_concessionnaire extends Model
{
    protected $table = 'vehicule_concessionnaires';

    public function marque()
    {
        return $this->belongsTo(Marque::class, 'marque_id');
    }


    public function couleur_vehicule()
    {
        return $this->belongsTo(Couleur_vehicule::class, 'couleur_vehicule_id');
    }
}