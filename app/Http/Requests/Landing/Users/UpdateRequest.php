<?php

namespace App\Http\Requests\Landing\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                'username' => 'sometimes|required|string|max:50', //TODO SE CAMBIÓ A SOMETIMES PARA PODER HACER TESTING
                'description' => 'sometimes',
                'phone_visible' => 'sometimes|required|boolean',
                'profession' => 'sometimes|required|string',
                'city' => 'sometimes|required|string',
                'address' => 'sometimes|required|string',
                'street' => 'sometimes|required|string', //TODO EXIST?
                'country_code' => 'sometimes|required|string|min:2',
                'phone_number' => 'sometimes|required|string|between:7,15',
                'phone_number_whatsapp'=>'sometimes|required|string|between:7,15',
                'country_code_whatsapp'=>'sometimes|required|string|min:2',
                'company_name' => 'sometimes|required|string',
                'cif_nif' => [
                    Rule::excludeIf(function() {
                        return $this->user()->hasRole('user');
                    }),            
                    'required',
                    'string',
                    // 'nif_correct',
                    'max:20',
                    // 'unique:users'
                ],
                'lat' => 'sometimes|required|string',
                'long' => 'sometimes|required|string',
                'zip_code' => 'sometimes|required|string',
                'province' => 'sometimes|required|string',
                'cv' => 'sometimes|required|file|'.config('filesystems.rules_files_accepted'),
                'picture' => 'sometimes|required|image|mimes:jpg,jpeg,png',
                'find_job' => 'sometimes',                
                'hide_address'=>'sometimes|required|boolean'
        ];
    }
}
