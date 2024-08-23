<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AnswerPostulation extends Model
{
    use HasFactory,SoftDeletes;
     protected $attributes = array(
        'status' => true,
    );
    protected $fillable = [      
        'question_id',
        'postulation_id',
        'answer',
        'status'
    ];


     public function question()// el nombre de antes :questionPostulation
    {
        return $this->belongsTo(QuestionPost::class, 'question_id');
    }

     public function postulation()
    {
        return $this->belongsTo(Postulation::class,'postulation_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }
}
