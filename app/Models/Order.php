<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory,SoftDeletes;
     
    protected $fillable = [
        'order_id',
        'name',
        'email',
        'card_number',
        'card_exp_month',
        'card_exp_year',
        'plan_name',
        'plan_id',
        'price',
        'price_currency',
        'txn_id',
        'payment_type',
        'payment_status',
        'receipt',
        'user_id',
    ];

    public function users()
    {
        return $this->hasOne(User::class, 'id');
    }

    public function plan()
    {
        return $this->hasOne(Plan::class, 'id');
    }

    public function scopeActivos($query) {
        return $query->where('status', 1);
    }
}
