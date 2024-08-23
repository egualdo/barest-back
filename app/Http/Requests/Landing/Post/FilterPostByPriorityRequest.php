<?php

namespace App\Http\Requests\Landing\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterPostByPriorityRequest extends FormRequest
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
            'keyword' => 'sometimes|required|string',
            'category' => 'sometimes|required|numeric|exists:category_level_2,id',
            'type_post' => 'sometimes|required|numeric|exists:post_types,id',
            'user_id' => 'sometimes|required|numeric|exists:users,id',
            'sort' => ['sometimes', 'required', 'string', Rule::in($allowedSortValues)]
        ];
    }
}
