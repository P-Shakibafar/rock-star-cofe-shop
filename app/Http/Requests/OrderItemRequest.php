<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderItemRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'quantity'        => ['sometimes', 'required', 'integer'],
            'options'         => ['sometimes', 'required', 'array'],
            'options.*.name'  => ['required', 'string', 'exists:options,name'],
            'options.*.value' => ['required', 'string', 'exists:option_values,value'],
        ];
    }
}
