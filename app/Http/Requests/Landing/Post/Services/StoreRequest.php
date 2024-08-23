<?php

namespace App\Http\Requests\Landing\Post\Services;

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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category_1_id' => 'required|numeric|exists:category_level_1,id',
            'category_2_id' => 'sometimes|required|numeric|exists:category_level_2,id',
            'city' => 'required|string',
            'address' => 'sometimes|required|string',
            'hour_price'=> 'sometimes|required|numeric',
            'delivery_price'=> 'sometimes|required|numeric',
            'working_holidays'=> 'sometimes|required|boolean',
            'custom_budget'=>'sometimes|required|boolean',
            'more_info' => 'sometimes|required|string',
        ];
    }

    /**
     * Prepare inputs for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'working_holidays' => $this->toBoolean($this->working_holidays),
            'custom_budget' => $this->toBoolean($this->custom_budget),
        ]);
    }

    /**
     * Convert to boolean
     *
     * @param $booleable
     * @return boolean
     */
    private function toBoolean($booleable)
    {
        return filter_var($booleable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
