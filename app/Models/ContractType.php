<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
    ];

    function posts() {
         return $this->belongsToMany(Post::class, 'post_contract_types','contract_type_id','post_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }
}
