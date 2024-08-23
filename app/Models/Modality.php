<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Modality extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'status'
    ];

    public function posts(){
        return $this->hasMany(Post::class, 'modality_id');
    }

    public function scopeActivos($query) {
        return $query->where('active', 1);
    }

}
