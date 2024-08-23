<?php

namespace App\Http\Requests\Landing\Post\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'category_1_id' => 'sometimes|required|numeric',
            'category_2_id' => 'sometimes|required|numeric',
            'category_3_id' => 'sometimes|required|numeric',
            'pictures' => 'sometimes|required|array',            
            'amount' => 'required|numeric',
            'lat' => 'sometimes|required|numeric',
            'long' => 'sometimes|required|numeric',
            'country' => 'sometimes|required|string', 
            'state' => 'sometimes|required|string',
            'province' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'street' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'zip_code' => 'sometimes|required|string',
            'priority' => 'sometimes|required|numeric',
            'condition_product_id' => 'required|numeric',
        ];
    }
}
