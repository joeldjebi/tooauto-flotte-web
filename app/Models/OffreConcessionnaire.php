<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffreConcessionnaire extends Model
{
    use HasFactory;

    protected $table = 'offre_concessionnaires';

    protected $fillable = [
        'fichier',
        'concessionnaire_id',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function concessionnaire()
    {
        return $this->belongsTo(Concessionnaire::class, 'concessionnaire_id');
    }

    public function user()
    {
        return $this->belongsTo(GestionnaireDeFlotte::class, 'user_id');
    }
}