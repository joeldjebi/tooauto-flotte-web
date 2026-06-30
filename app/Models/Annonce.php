<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    protected $table = 'annonces';

    public function type_de_piece()
    {
        return $this->belongsTo(Type_de_piece::class, 'type_de_piece_id');
    }

    public function categorie_piece()
    {
        return $this->belongsTo(Categorie_piece::class, 'categorie_piece_id');
    }

    public function sous_categorie_piece()
    {
        return $this->belongsTo(Sous_Categorie_piece::class, 'sous_categorie_piece_id');
    }

    public function marque()
    {
        return $this->belongsTo(Marque::class, 'marque_id');
    }

}