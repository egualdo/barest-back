<?php

namespace App\Http\Requests\Admin\User;

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required|string',
            'country_code' => 'required|string|min:1',
            'phone_number' => 'required|numeric',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'access' => 'required|array',
        ];
    }
}
