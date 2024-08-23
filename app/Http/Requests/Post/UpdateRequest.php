<?php

namespace App\Http\Requests\Post;

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
     * @return array
     */
    public function rules()
    {
        return [
            'title'=>'required|max:255',
            'description'=>'required|max:255',
            // 'user_id'=>'nullable',//dueno del post
            'picture'=>'nullable|string',
            // 'type_post_id'=>'nullable',//tipo de post producto o servicio o jobs
            // 'stage_post_id'=>'nullable|integer',//etapa del post
            'type_payment'=>'nullable|integer',//tipo de pago quincenal , mensual , por hora , etc...
            'amount'=>'required|numeric',
            'category_id'=>'required|integer',//hoteles,bares,cafeterias, restaurantes etc...
            'experience'=>'nullable|integer',//0-1,1-3,+5 annos , etc...
            'contract_type'=>'sometimes',// indefinido, part-time, full-time, freelancer etc...
            'lat'=>'nullable|numeric',
            'long'=>'nullable|numeric',
            'country' => 'sometimes|required|string',
            'state'=>'nullable|string',
            'city'=>'nullable|string',
            'street'=>'nullable|string',
            'address'=>'nullable|string',
            'requeriments'=>'nullable',
            'priority'=>'nullable|integer',
            'modality_id'=>'nullable|integer'
    //    'status'=>'',
        ];
    }
}
