<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PostType extends Model
{
    use HasFactory,SoftDeletes;

     protected $fillable = [
        'name',
        'status',
    ];


     public function posts()
    {
        return $this->hasMany(Post::class, 'post_type_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }
}
