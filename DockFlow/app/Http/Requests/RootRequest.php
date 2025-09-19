<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RootRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'code' => 'required',
            'type_id' => 'required',
            'parent_id' => 'required',
            'level' => 'required|numeric',
        ];
    }
}
