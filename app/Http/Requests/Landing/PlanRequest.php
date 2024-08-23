<?php

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
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
            // 'name'=>'required|unique:plans|max:255',
        // 'price',
        // 'duration',
        // 'max_adversitements'=>'nullable|integer',
        // 'color'=>'required|max:255',
        // 'description'=>'required|max:255',
        ];
    }
}