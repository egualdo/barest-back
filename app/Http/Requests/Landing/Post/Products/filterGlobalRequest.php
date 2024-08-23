<?php

namespace App\Http\Requests\Landing\Post\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class filterGlobalRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $allowedSortValues = [
            'amount-desc',
            'amount-asc',
            'created_at-asc',
            'created_at-desc',
        ];

        return [
            'title' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|numeric',
            'lat' => 'sometimes|required|numeric',
            'long' => 'sometimes|required|numeric',
            'range_b' => 'sometimes|required|numeric',
            'sort' => ['sometimes', 'required', 'string', Rule::in($allowedSortValues)]
        ];
    }
}
