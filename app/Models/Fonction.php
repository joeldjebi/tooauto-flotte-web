<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fonction extends Model
{
    protected $table = 'fonctions';

    public function type_alert()
    {
        return $this->HasMany(Chauffeur::class, 'fonction_id');
    }

}
