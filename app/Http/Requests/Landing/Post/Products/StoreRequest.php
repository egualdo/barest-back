<?php

namespace App\Http\Requests\Landing\Post\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category_1_id' => 'sometimes|required|numeric|exists:category_level_1,id',
            'category_2_id' => 'sometimes|required|numeric|exists:category_level_2,id',
            'category_3_id' => 'sometimes|required|numeric|exists:category_level_3,id',
            'pictures' => 'required|array',
            'amount' => 'required|numeric',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'state' => 'sometimes|required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'street' => 'required|string',
            'address' => 'required|string',
            'zip_code' => 'sometimes|required|string',
            'condition_product_id' => 'required|numeric|exists:condition_products,id',
            'country' => 'sometimes|required|string',
        ];
    }
}
