<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'CR' => 'nullable|string|max:255',
            'vatNumber' => 'nullable|string|max:255',
            'national_address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'building_number' => 'nullable|string|max:255',
            'secondary_number' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'short_address' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id'
        ];
    }
}
