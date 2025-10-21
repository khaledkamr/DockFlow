<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'NID' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ];
    }
}
