<?php

namespace App\Http\Requests\Landing\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnswerRequest extends FormRequest
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
            'review_id' => 'required|numeric|exists:reviews,id',
            'answered_at' => 'required|date_format:Y-m-d H:i:s',
            'answer' => 'required|string',
        ];
    }
}