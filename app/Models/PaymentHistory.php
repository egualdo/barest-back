<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "transaction_number",
        "concept",
        "mount",
        "status",
        "payment_method"
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return PaymentFactory::new();
    }

    /**
     * Get the parent payable model (product or plan).
     */
    public function payable()
    {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
