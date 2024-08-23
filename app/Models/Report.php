<?php

namespace App\Models;

use Database\Factories\ReviewReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [ 
        'creator_user_id',
        'reported_user_id',
        'motive_id',
        'other_motive',
        'status'
    ];

    protected $attributes = [
        'status' => 'PENDANT'
    ];

    public function reportable() {
    	return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function reported()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function motive()
    {
        return $this->belongsTo(Motive::class, 'motive_id');
    }

    //  public function categories()
    // {
    //     return $this->hasOne(Category::class,'id','category_id'); //,'id','post_id'
    //     //  return $this->hasOne(User::class,'id','receiver_id');
    // }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }

}
