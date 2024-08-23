<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'benefits',
        'description',
    ];

    /**
     * Get the plans benefit.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function benefits(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
        );
    }

    /**
     * Get the plans active status.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function active(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? true : false,
        );
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    
    public function scopeActivos($query) {
        return $query->where('active', 1);
    }

    public function prices() {
        return $this->hasMany(PlanPrice::class, 'plan_id');
    }

}

