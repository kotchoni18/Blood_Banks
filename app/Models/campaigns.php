<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class campaigns extends Model
{
     protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'address',
        'target_donations',
        'current_donations',
        'status',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function appointments()
    {
        return $this->hasMany(appointments::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
                    ->where('start_date', '>', now());
    }

    // Accessors
    public function getProgressPercentageAttribute()
    {
        if ($this->target_donations == 0) return 0;
        return min(100, ($this->current_donations / $this->target_donations) * 100);
    }
    //
}
