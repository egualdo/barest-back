<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'range'=> 'array',
        'status'

    ];

    public static function experienceRangeToFilter(&$experiencesToFilter, $experience_id) {

        if($experience_id == 1) {
            array_push($experiencesToFilter, $experience_id);
            return;
        }

        $experience = self::find($experience_id);
        $experienceAll = self::all();

        foreach ($experienceAll as $experienceItem) {
            if($experienceItem->minimum >= $experience->minimum)
                array_push($experiencesToFilter, $experienceItem->id);
        }
    }

    public function posts()
    {
        return $this->hasMany(Post::class,'experience_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }

}
