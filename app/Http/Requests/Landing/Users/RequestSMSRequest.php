<?php

namespace App\Http\Requests\Landing\Users;

use Illuminate\Foundation\Http\FormRequest;

class RequestSMSRequest extends FormRequest
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
            'country_code' => 'required|string|max:4',
            'phone_number' => 'required|string|min:7|max:15',
            'user_id' => 'required',
        ];
    }
}
