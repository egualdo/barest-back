<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [//anuncios
        'name',
        'url',//imagen
        'user_id',
        'plan_id',//id del plan
        'status'

    ];

     public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }

}
