<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='user_comments';

     protected $fillable = [
        'review_id',
        'created_by',
        'body',
        'status'
    ];

    public function review()
    {
        return $this->belongsTo(Review::class,'review_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }

}
