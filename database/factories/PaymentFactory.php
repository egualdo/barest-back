<?php

namespace Database\Factories;

use App\Models\PaymentHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentHistory>
 */
class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $concepts = [
            'Suscrición de plan',
            'Compra de paquete',
            'Compra de oferta urgente '
        ];

        $payment_methods = [
            'Paypal',
            'Stripe',
        ];

        return [
            "user_id" => User::get()->random()->id,
            "transaction_number" => Str::random(16),
            "mount" => fake()->randomFloat(2, 1.00, 120.00),
            "concept" => $concepts[ array_rand($concepts, 1) ],
            "payment_method" => $payment_methods[ array_rand($payment_methods, 1) ],
            "status" => 'approved',
            "created_at" => fake()->dateTimeThisDecade(),
            "updated_at" => fake()->dateTimeThisDecade(),
        ];
    }
}
