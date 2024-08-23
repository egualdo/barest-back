<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Validation\Rule;
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required|string',
            'country_code' => 'required|numeric|min:1',
            'phone_number' => 'required|numeric',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user())
            ],
            'password' => 'sometimes|required|string|confirmed',
            'access' => 'required|array',
        ];
    }
}
