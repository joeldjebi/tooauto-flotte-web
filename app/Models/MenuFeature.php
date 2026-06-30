<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuFeature extends Model
{
    protected $table = 'menu_features';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'libelle',
        'groupe',
        'ordre',
    ];

    public static function items(): array
    {
        return [
            'dashboard' => ['libelle' => 'Tableau de bord', 'groupe' => 'Accueil'],
            'vehicules' => ['libelle' => 'Véhicules', 'groupe' => 'Opérations'],
            'autodocs' => ['libelle' => 'Documents auto', 'groupe' => 'Opérations'],
            'pieces' => ['libelle' => 'Pièces & accessoires', 'groupe' => 'Opérations'],
            'entretiens' => ['libelle' => 'Entretien', 'groupe' => 'Services flotte'],
            'assistances' => ['libelle' => 'Assistance', 'groupe' => 'Services flotte'],
            'reparations' => ['libelle' => 'Réparations & Suivi', 'groupe' => 'Services flotte'],
            'carburants' => ['libelle' => 'Carburant & Conso.', 'groupe' => 'Services flotte'],
            'alertes' => ['libelle' => 'Toutes les alertes', 'groupe' => 'Alertes'],
            'alerte_assurance' => ['libelle' => 'Assurance', 'groupe' => 'Alertes'],
            'alerte_vidange' => ['libelle' => 'Vidange', 'groupe' => 'Alertes'],
            'alerte_visite' => ['libelle' => 'Visite technique', 'groupe' => 'Alertes'],
            'alerte_controle' => ['libelle' => 'Contrôle technique', 'groupe' => 'Alertes'],
            'prestataires' => ['libelle' => 'Prestataires', 'groupe' => 'Réseau'],
            'concessionnaires' => ['libelle' => 'Concessionnaires', 'groupe' => 'Réseau'],
            'offres' => ['libelle' => 'Offres', 'groupe' => 'Réseau'],
            'fonctions' => ['libelle' => 'Fonctions', 'groupe' => 'Utilisateurs'],
            'roles' => ['libelle' => 'Rôles & permissions', 'groupe' => 'Utilisateurs'],
            'admin_users' => ['libelle' => 'Users admin', 'groupe' => 'Utilisateurs'],
            'utilisateurs' => ['libelle' => 'Chauffeurs', 'groupe' => 'Utilisateurs'],
            'profil' => ['libelle' => 'Mon profil', 'groupe' => 'Paramètres'],
            'password' => ['libelle' => 'Mot de passe', 'groupe' => 'Paramètres'],
            'documentation' => ['libelle' => 'Documentation', 'groupe' => 'Aide'],
        ];
    }
}
