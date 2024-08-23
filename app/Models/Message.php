<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [        
                'text',
                'chat_room_id',
                'creator_id',
            ];

    public function sender()
    {
        return $this->belongsTo(User::class,'creator_id');
    }

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }

    public function markAsRead() {
        $this->readed = true;
        $this->update();
    }
}
