<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;

class BasicPagination extends FormRequest
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
        return ['sort'        => ['nullable',
                                  'string',
                                  'regex:/[\w\s]+\|(a|de)sc/'],
                'page'        => ['nullable',
                                  'numeric',
                                  'min:1'],
                'per_page'    => ['nullable',
                                  'numeric',
                                  'min:' . Controller::MIN_ITEMS_PER_PAGE,
                                  'max:' . Controller::MAX_ITEMS_PER_PAGE],
                'no_paginate' => ['nullable',
                                  'boolean']];
    }
}
