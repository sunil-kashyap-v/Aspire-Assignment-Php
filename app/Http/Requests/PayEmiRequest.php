<?php

namespace App\Http\Requests;


use Dingo\Api\Http\FormRequest;

class PayEmiRequest extends FormRequest
{
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
            'loan_id' => 'required|numeric',
            'amount' => 'required',
            'date' => 'required|date_format:Y-m-d'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }
}