<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'CR' => 'required|max:255',
            'vat_number' => 'required|max:255',
            'national_address' => 'required',
            'phone' => 'max:15',
            'email' => 'max:255',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
