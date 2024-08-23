<?php

namespace App\Http\Requests\Landing\Postulation;

use App\Enums\StagePostulationEnum;
use Illuminate\Foundation\Http\FormRequest;
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [            
            'stage_id'=>['sometimes','required',(new Enum(StagePostulationEnum::class))],
            'cv'=>'sometimes|required|file|'.config('filesystems.rules_files_accepted'),
            'notes'=>'sometimes|required|string|max:255'
        ];
    }
}
