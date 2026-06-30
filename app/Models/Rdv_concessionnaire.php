<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rdv_concessionnaire extends Model
{
    use HasFactory;

    protected $table = 'rdv_concessionnaires';

    protected $fillable = [
        'jour',
        'heure',
        'gestionnaire_de_flotte_id',
        'concessionnaire_id',
    ];

    protected $casts = [
        'heure' => 'datetime',
    ];

    // Relation avec le gestionnaire de flotte
    public function gestionnaire_de_flotte()
    {
        return $this->belongsTo(GestionnaireDeFlotte::class, 'gestionnaire_de_flotte_id');
    }

    // Relation avec le concessionnaire
    public function concessionnaire()
    {
        return $this->belongsTo(Concessionnaire::class, 'concessionnaire_id');
    }
}