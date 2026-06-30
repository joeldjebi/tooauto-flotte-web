<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConcessionnaire extends Model
{
    use HasFactory;

    protected $table = 'userconcessionnaires';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'statut',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relation avec les rendez-vous
    public function rdv_concessionnaires()
    {
        return $this->hasMany(Rdv_concessionnaire::class, 'concessionnaire_id');
    }
}
