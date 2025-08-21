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
            'contract_id' => 'required',
            'customer_id' => 'required',
            'date' => 'required',
            'driver_name' => 'required',
            'driver_NID' => 'required',
            'driver_car' => 'required',
            'car_code' => 'required',
            'storage_price' => 'required',
            'late_fee' => 'required', 
            'tax' => 'required'  
        ];
    }
}
