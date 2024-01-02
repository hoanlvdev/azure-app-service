<?php

namespace App\Http\Requests;

class PaginationRequest extends APIRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'integer',
            'perPage' => 'integer',
            'sortValidation' => 'array|nullable',
            'sortValidation.*.property' => 'required|string',
            'sortValidation.*.direction' => 'required|string',
            'filterValidation' => 'array|nullable',
            'filterValidation.*.property' => 'string',
            'filterValidation.*.anyMatch' => 'boolean',
            'filterValidation.*.value' => 'string',
        ];
    }
}