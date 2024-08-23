<?php

namespace App\Http\Requests\Landing\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterPostsByPriorityAndTypeRequest extends FormRequest
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
            'priority' => 'required|string',
            'postTypeId' => 'required|numeric',
            'title' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|numeric',
            'user_id' => 'sometimes|required|numeric',
            'sort' => ['sometimes', 'required', 'string', Rule::in($allowedSortValues)]
        ];
    }
}
