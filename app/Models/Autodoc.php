<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autodoc extends Model
{
    protected $table = 'autodocs';

    public function gestionnaire_de_flotte()
    {
        return $this->belongsTo(GestionnaireDeFlotte::class, 'user_id');
    }

    public function type_docauto()
    {
        return $this->belongsTo(Type_docauto::class, 'type_docauto_id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }
}