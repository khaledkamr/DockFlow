<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'branch' => 'required|max:255',
            'CR' => 'required',
            'TIN' => 'required',
            'national_address' => 'required',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:15'
        ];
    }
}
