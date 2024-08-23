<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='category_level_2';

    protected $fillable = [
        'name',
        'group_id',
        'featured',
        'status',
        'only_market',
        'icon'
    ];

    // public function getRouteKeyName()
    // {
    //     return 'name';
    // }

    public function categoryLevel3()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }   

    public function categoryLevel1()
    {
        return $this->belongsTo(GroupCategory::class, 'group_id');
    }

    public function posts() {
        return $this->hasMany(Post::class, 'category_2_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }   

}
