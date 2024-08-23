<?php

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id'=>'required|unique:orders|max:100',
            'name'=>'nullable|max:100',
            'email'=>'nullable|max:100',
            'card_number'=>'nullable|max:10',
            'card_exp_month'=>'nullable|max:10',
            'card_exp_year'=>'nullable|max:10',
            'plan_name'=>'required|max:100',
            'plan_id'=>'required|integer',
            'price'=>'required|float',
            'price_currency'=>'required|max:10',
            'txn_id'=>'required|max:100',
            'payment_type'=>'required|max:100',
            'payment_status'=>'required|max:100',
            'receipt'=>'nullable|max:255',
            'user_id'=>'nullable|integer'
        ];
    }
}