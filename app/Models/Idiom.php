<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Idiom extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'idioms';

    protected $fillable = [
        'name',
    ];

    public function post()
    {
        return $this->belongsToMany(Post::class,'idiom_posts','idiom_id','post_id');

        //  return $this->belongsToMany(Regions::class, 'regions_stores', 'stores_id', 'regions_id');
    }
}

