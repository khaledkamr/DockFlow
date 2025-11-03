<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_id' => 'nullable',
            'customer_id' => 'required',
            'user_id' => 'required',
            'date' => 'required',
            'type' => 'required',
            'driver_name' => 'required',
            'driver_NID' => 'required',
            'driver_car' => 'required',
            'car_code' => 'required',
            'tax_statement' => 'nullable|string',
            'storage_price' => 'nullable|numeric',
            'storage_duration' => 'nullable|numeric',
            'late_fee' => 'nullable|numeric',
        ];
    }
}
