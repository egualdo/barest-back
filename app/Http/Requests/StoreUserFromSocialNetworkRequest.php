<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserFromSocialNetworkRequest extends FormRequest
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
                'name'=>'required|string|max:255',
                'email'=>'required|string|max:255',
                'avatar'=>'required|string|url',
                'social_network'=>'required|string|max:255',
                'social_network_user_id'=>'required|string',
                'type_role'=>'required|string|max:255'
        ];
    }
}
