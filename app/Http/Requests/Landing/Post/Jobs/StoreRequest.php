<?php

namespace App\Http\Requests\Landing\Post\Jobs;

use App\Enums\PostLevelIdiomEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category_1_id' => 'sometimes|required|numeric|exists:category_level_1,id',
            'category_2_id' => 'sometimes|required|numeric|exists:category_level_2,id',
            'category_3_id' => 'sometimes|required|numeric|exists:category_level_3,id',
            'amount' => 'required|numeric|min:0',
            'amount_to' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $amount = $this->input('amount');
                    if (!empty($amount) && $value < $amount) {
                        $fail('El campo amount_to debe ser mayor o igual que el campo amount.');
                    }
                }
            ],
            'experience_id' => [
                'sometimes',
                'required',
                'numeric',
                Rule::excludeIf(function() {
                    return $this->user()->hasRole('user');
                }),
                'exists:experiences,id'
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
            'state' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'requeriments' => 'sometimes|required|string',
            'modality_id' => 'sometimes|required|numeric|exists:modalities,id',
            'payment_modality_id' => 'sometimes|required|numeric|exists:payment_modalities,id',
            'cv'=>'sometimes|required|file|'.config('filesystems.rules_files_accepted'),
            'idioms' => 'sometimes|required|array',
            'idioms.*.name' => 'required|string',
            'idioms.*.level'=>[
                'required',
                'string',
                (new Enum(PostLevelIdiomEnum::class))
            ],
            'questions' => 'sometimes|required|array',
            'questions.*.question' => 'required|string',
            'country' => 'sometimes|required|string',
            'benefits' => 'sometimes|required|string'
        ];
    }
}
