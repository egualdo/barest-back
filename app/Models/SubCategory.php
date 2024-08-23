<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='category_level_3';

    protected $fillable = [
        'name',
        'category_id',
        'status'
    ];

    public function categoryLevel2()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }

    public function posts() {
        return $this->hasMany(Post::class, 'category_3_id');
    }
    
}
