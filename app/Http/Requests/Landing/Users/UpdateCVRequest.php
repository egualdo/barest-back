<?php

namespace App\Http\Requests\Landing\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCVRequest extends FormRequest
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
            'cv'=> 'required|file|'.config('filesystems.rules_files_accepted'),
        ];
    }
}
