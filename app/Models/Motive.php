<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Motive extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'type',//cancelacion,reporte,block
        'status'
    ];

    // public function cancelledPostulations()
    // {       
    //     return $this->hasMany(CancelledPostulation::class, 'motive_id');        
    // }

    public function reports()
    {       
        return $this->hasMany(Report::class, 'motive_id');        
    }

    public function scopeActivos($query) {
        return $query->where('active', 1);
    }

    public function scopeReview($query) {
        return $query->where('entity', 'review');
    }
}
