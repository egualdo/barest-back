<?php

namespace App\Http\Requests\Landing\Review;

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
            'ranked_user_id' => 'required|numeric',
            'ranking' => 'required|numeric',            
            'comment'  => 'sometimes|required|string|max:255',
            'post_id' => 'required|numeric|exists:posts',
        ];
    }
}