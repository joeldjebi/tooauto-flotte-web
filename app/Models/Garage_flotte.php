<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garage_flotte extends Model
{
    protected $table = 'garage_flottes';

        // Attributs pouvant être assignés en masse
        protected $fillable = [
            'name',
            'gestionnaire_de_flotte_id',
            'adresse',
            'adresse_map',
            'contact',
            'statut',
        ];

}