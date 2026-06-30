<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $table = 'alerts';

    public function type_alert()
    {
        return $this->belongsTo(Type_alert::class, 'type_alert_id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }

    public function gestionnaire_de_flotte()
    {
        return $this->belongsTo(GestionnaireDeFlotte::class, 'gestionnaire_de_flotte_id');
    }

    public function user()
    {
        return $this->belongsTo(Chauffeur::class, 'user_id');
    }
}