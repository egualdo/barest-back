<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class GroupCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='category_level_1';

    protected $fillable = [
        'name',
        'status'

    ];

    public function categoryLevel2()
    {
        return $this->hasMany(Category::class, 'group_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }

    public function posts() {
        return $this->hasMany(Post::class, "category_1_id");
    }

}
