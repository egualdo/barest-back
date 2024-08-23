<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'ranked_user_id',
        'answer',
        'ranking', //puntuacion
        'comment',
        'reviewer_user_id', //quien lo califico
        'post_id',//categoria a la que pertenece el user calificado esa viene en el request
        'status'
    ];
    //reviewer
    public function user()
    {
        return $this->belongsTo(User::class,'reviewer_user_id');
    }

    public function rankedUser()
    {
        return $this->belongsTo(User::class,'ranked_user_id');        
    }

    public function post()
    {
        return $this->belongsTo(Post::class,'post_id');         
    }

    public function scopeActivos($query) {
        return $query->where('deleted_at', null);
    }

    public function report_received() {
        return $this->morphOne(Report::class, 'reportable');
    }
}
