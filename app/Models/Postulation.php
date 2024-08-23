<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Postulation extends Pivot
{
    use SoftDeletes;
    
    protected $table = 'postulations';
    
    public $incrementing = true;
    
    // protected $attributes = array(
    //     'status' => true,
    // );

    protected $fillable = [
        'stage_id',//Inscrito, Visto, No seleccionado, Proceso finalizado.
        // 'status',
        'created_at',
        'updated_at',
        'user_id',
        'notes'
        // 'cv'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class,'post_id');
    }

    public function answers(){//nueva
        return $this->hasMany(AnswerPostulation::class,'postulation_id');
    }

    // public function cancelation(){//nueva
    //     return $this->hasOne(CancelledPostulation::class,'postulation_id');
    // }

    public function resume()
    {
        return $this->morphOne(Resume::class, 'resumeable');
    }

    public function motive()
    {
        return $this->morphOne(Motive::class, 'motiveable');
    }

    // public function getCvAttribute($value) {
    //     if( is_null( $value ) || $value == "" )
    //         return NULL;

    //     return str_contains($value,'http') ? $value : Storage::disk('s3')->url( $value );
    // }
    
}
