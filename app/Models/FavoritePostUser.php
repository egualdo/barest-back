<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FavoritePostUser extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id',
        'post_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

     public function post()
    {
        return $this->belongsTo(Post::class,'post_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }
}
