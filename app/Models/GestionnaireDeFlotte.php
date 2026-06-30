<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class GestionnaireDeFlotte extends Authenticatable
{
    use HasFactory, Notifiable;  // Retirez HasApiTokens car il n'est pas nécessaire

    protected $table = 'gestionnaire_de_flottes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenoms',
        'role',
        'parent_gestionnaire_id',
        'fleet_role_id',
        'statut',
        'mobile',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function fleet_role()
    {
        return $this->belongsTo(FleetRole::class, 'fleet_role_id');
    }

    public function parent_gestionnaire()
    {
        return $this->belongsTo(self::class, 'parent_gestionnaire_id');
    }

    public function admin_users()
    {
        return $this->hasMany(self::class, 'parent_gestionnaire_id');
    }

    public function fleetOwnerId(): int
    {
        return (int) ($this->parent_gestionnaire_id ?: $this->id);
    }

    public function isMainAdmin(): bool
    {
        return empty($this->parent_gestionnaire_id) && in_array(strtolower((string) $this->role), ['01', '1', 'sa', 'super_admin'], true);
    }
}
