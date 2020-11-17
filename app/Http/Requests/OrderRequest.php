<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest {

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
        if( $this->getMethod() == 'POST' ) {
            return $this->createRules();
        }
        if( $this->getMethod() == 'PUT' || $this->getMethod() == 'PATCH' ) {
            return $this->updateRules();
        }
    }

    public function createRules()
    {
        return [
            'items'                   => ['required', 'array'],
            'items.*'                 => [
                'quantity'   => ['required', 'integer'],
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'unit_price' => ['required', 'integer'],
                'options'    => ['required', 'array'],
            ],
            'items.*.options.*.name'  => ['required', 'string', 'exists:options,name'],
            'items.*.options.*.value' => ['required', 'string', 'exists:option_values,value'],
        ];
    }

    public function updateRules()
    {
        return [
            'status' => 'required|string',
        ];
    }
}
