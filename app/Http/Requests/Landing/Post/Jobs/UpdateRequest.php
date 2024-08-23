<?php

namespace App\Http\Requests\Landing\Post\Jobs;

use App\Enums\PostLevelIdiomEnum;
use App\Enums\PostStatuses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category_1_id' => 'sometimes|required|numeric',
            'category_2_id' => 'sometimes|required|numeric',
            'category_3_id' => 'sometimes|required|numeric',
            'picture' => 'sometimes|required|array',            
            'amount' => 'required|numeric',
            'amount_to' => 'required|numeric',
            'experience_id' => [
                'sometimes',
                'required',
                'numeric',
                Rule::excludeIf(function() {
                    return $this->user()->hasRole('user');
                })
            ],
            'years_experience' => [
                'sometimes',
                'required',
                'numeric',
                Rule::excludeIf(function() {
                    return $this->user()->hasRole('company');                        
                })
            ],
            'contract_types' => 'sometimes|required|array',
            'lat' => 'sometimes|required|string',
            'long' => 'sometimes|required|string',
            'country' => 'sometimes|required|string',
            'state' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'street' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'requeriments' => 'sometimes|required|string',
            'priority' => 'sometimes|required|numeric',
            'modality_id' => 'sometimes|required|numeric',
            'payment_modality_id' => 'sometimes|required|numeric',
            'condition_product_id' => 'sometimes|required|numeric',
            'cv'=>'sometimes|required|file|'.config('filesystems.rules_files_accepted'),
            'idioms' => 'sometimes|required|array',
            'idioms.*.name' => 'required|string',
            'idioms.*.level'=>[
                'required',
                'string',
                (new Enum(PostLevelIdiomEnum::class))
            ],'country' => 'sometimes|required|string',
            'status'=>['sometimes','required',(new Enum(PostStatuses::class))],
            'benefits' => 'sometimes|required|string',
            'questions' => 'sometimes|required|array',
            'questions.*.question' => 'sometimes|required|string',
            'questions.*.id' => 'sometimes|required|integer'
        ];
    }
}
