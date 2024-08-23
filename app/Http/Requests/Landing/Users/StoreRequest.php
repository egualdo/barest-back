<?php

namespace App\Http\Requests\Landing\Users;

use App\Enums\UserRolesEnum;
use App\Enums\UserStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username'=>'required|string|max:50',
            'email'=>'required|max:255',
            'password'=>'required|min:8',
            'description'=>'sometimes|required|max:255',
            'online'=>'sometimes|required|boolean',
            'phone_visible'=>'sometimes|required|boolean',
            'phone_visible_whatsapp'=>'sometimes|required|boolean',
            'country_code'=>'sometimes|required|string|min:2',
            'phone_number'=>'sometimes|required|string|between:7,15',
            'country_code_whatsapp'=>'sometimes|required|string|min:2',
            'phone_number_whatsapp'=>'sometimes|required|string|between:7,15',
            'profession'=>'sometimes|required|max:255',
            'province'=>'sometimes|required|max:255',
            'city'=>'sometimes|required|max:255',
            'zip_code'=>'sometimes|required|max:255',
            'type_role'=>['required',(new Enum(UserRolesEnum::class))],
            'lat'=>'sometimes|required',
            'long'=>'sometimes|required',
            'company_name'=>'sometimes|required|max:255',
            'cif_nif'=> [
                'sometimes',
                'required',
                'string',
                // 'nif_correct',
                'max:255',
                // 'unique:users'
            ],
            'address'=>'sometimes|required|max:255',
            'hide_address'=>'sometimes|required|boolean'
        ];
    }
}
