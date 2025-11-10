<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'building_number' => 'nullable|string|max:20',
            'secondary_number' => 'nullable|string|max:20',
            'short_address' => 'nullable|string|max:255',
        ];
    }
}
