<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class blood_stocks extends Model
{
    protected $fillable = [
        'blood_group',
        'quantity_units',
        'expiry_date',
        'status',
        'location',
        'temperature',
        'collection_date',
        'notes'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'collection_date' => 'date',
        'temperature' => 'decimal:1'
    ];

    // Scopes
    public function scopeByGroup($query, $bloodGroup)
    {
        return $query->where('blood_group', $bloodGroup);
    }

    public function scopeCritical($query)
    {
        return $query->where('status', 'critical');
    }

    public function scopeLow($query)
    {
        return $query->where('status', 'low');
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->whereDate('expiry_date', '<=', now()->addDays($days));
    }

    // Mutators
    public function setStatusAttribute($value)
    {
        // Auto-calculate status based on quantity
        if ($this->quantity_units < 50) {
            $this->attributes['status'] = 'critical';
        } elseif ($this->quantity_units < 100) {
            $this->attributes['status'] = 'low';
        } else {
            $this->attributes['status'] = 'good';
        }
    }

    // Accessors
    public function getDaysUntilExpiryAttribute()
    {
        return $this->expiry_date->diffInDays(now(), false);
    }
    //
}
