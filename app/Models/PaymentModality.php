<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentModality extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = [
        'name',
        'status'
    ];

     public function posts()//quincenal,mensual,por Hora etc...
    {
        return $this->hasMany(Post::class,'payment_modality_id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }

}
