<?php

namespace App\Http\Requests\Landing\Report;

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
            'reportable_id'=>'required|numeric',
            'motive_id'=>'sometimes|required|numeric',
            'other_motive'=>'sometimes|required|string'//other_motive
        ];
    }
}