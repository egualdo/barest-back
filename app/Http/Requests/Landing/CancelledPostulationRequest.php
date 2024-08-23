<?php

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;

class CancelledPostulationRequest extends FormRequest
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
            // 'user_id'=>'nullable|integer',
            'postulation_id'=>'nullable|integer',
            'motive_id'=>'nullable|integer',
            'other_motive'=>'sometimes|required'
        ];
    }
}