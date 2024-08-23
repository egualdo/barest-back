<?php

namespace App\Http\Requests\Landing\Category;

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
            'name'=>'required|max:255',
            'group_id'=>'required|numeric|exists:category_level_1,id',
            'featured'=>'sometimes|required|boolean',//validar por parte de dominyel en false si no lo selecciona
            'only_market'=>'sometimes|required|boolean',//validar por parte de dominyel en false si no lo selecciona
        ];
    }
}
