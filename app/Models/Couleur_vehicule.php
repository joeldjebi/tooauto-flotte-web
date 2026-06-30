<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Couleur_vehicule extends Model
{
    protected $table = 'couleur_vehicules';


    public function vehicules()
    {
        return $this->HasMany(Vehicule::class, 'couleur_vehicule_id');
    }
}