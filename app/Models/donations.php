<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class donations extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'agent_id',
        'blood_group',
        'donation_type',
        'quantity_ml',
        'hemoglobin_level',
        'blood_pressure',
        'weight',
        'medical_notes',
        'status',
        'consent_given',
        'medical_check_passed',
        'eligibility_verified',
        'donation_date'
    ];

    protected $casts = [
        'donation_date' => 'datetime',
        'consent_given' => 'boolean',
        'medical_check_passed' => 'boolean',
        'eligibility_verified' => 'boolean',
        'hemoglobin_level' => 'decimal:1',
        'weight' => 'decimal:2'
    ];

    // Relationships
    
    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('donation_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('donation_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByBloodGroup($query, $bloodGroup)
    {
        return $query->where('blood_group', $bloodGroup);
    }
    //

    
}
