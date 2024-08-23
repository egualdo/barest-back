<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class QuestionPost extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [        
        'question',
        'post_id',
        'status',
        'old'
    ];

    protected $attributes = [
        'status' => true,
    ];

    public function post()
    {
        return $this->belongsTo(Post::class,'post_id');
        //belongs to  class target ,   foreign key actual class
        
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }

}
