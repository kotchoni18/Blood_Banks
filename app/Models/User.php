<?php


namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        // Informations communes
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        
        // Informations générales
        'birth_date',
        'gender',
        'address',
        'city',
        
        // Donneur
        'blood_group',
        'last_donation_date',
        'donation_count',
        
        // Agent
        'department',
        'employee_number',
        'hire_date',
        
        // Admin
        'super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'last_donation_date' => 'date',
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'super_admin' => 'boolean',
        'donation_count' => 'integer',
    ];

    // Accesseurs pour le nom complet
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Méthodes de vérification de rôle
    public function isDonor()
    {
        return $this->role === 'donor';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'admin' && $this->super_admin === true;
    }

    // Scopes pour filtrer par rôle
    public function scopeDonors($query)
    {
        return $query->where('role', 'donor');
    }

    public function scopeAgents($query)
    {
        return $query->where('role', 'agent');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function donations()
    {
        return $this->hasMany(donations::class, 'donor_id'); //  préciser aussi
    }


}