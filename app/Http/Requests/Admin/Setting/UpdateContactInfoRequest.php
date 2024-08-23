<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactInfoRequest extends FormRequest
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
            'email' => 'required|email',
            'address' => 'required|string|max:255',
            'phone_number' => 'sometimes|required|string',
            'facebook_url' => [
                'required',
                'regex:/^((http(s)?:\/\/)|(www\.))\S+/i',
            ],
            'instagram_url' => [
                'required',
                'regex:/^((http(s)?:\/\/)|(www\.))\S+/i',
            ],
            'linkedin_url' => [
                'required',
                'regex:/^((http(s)?:\/\/)|(www\.))\S+/i',
            ],
        ];
    }
}
