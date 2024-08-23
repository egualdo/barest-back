<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        "name",
        "price",
        "month_duration"
    ];

    public function scopeDisponibles($query) {
        return $query->where('active', 1);
    }
}
