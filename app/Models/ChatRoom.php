<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [        
        'creator_id',// created_by user quien creo la sala
        'receiver_id',//usuario quien participa o destinatario
        'status'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function userCreator()
    {
        return $this->belongsTo(User::class,'creator_id');
    }

     public function addedUser()
    {
        return $this->belongsTo(User::class,'receiver_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }
}
