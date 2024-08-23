<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'requeriment_posts';

    protected $fillable = [
        'name',
    ];

    // public function post()
    // {
    //     return $this->belongsTo(Post::class,'post_id');
    // }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }
}
