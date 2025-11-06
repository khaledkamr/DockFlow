<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|string',
            'driver_id' => 'required_if:type,ناقل داخلي|nullable|exists:drivers,id',
            'vehicle_id' => 'required_if:type,ناقل داخلي|nullable|exists:vehicles,id',
            'supplier_id' => 'required_if:type,ناقل خارجي|nullable|exists:suppliers,id',
            'date' => 'required|date',
            'from' => 'required|string',
            'to' => 'required|string',
            'duration' => 'nullable|string',
            'notes' => 'nullable|string',
            'supplier_cost' => 'nullable|numeric|min:0',
            'diesel_cost' => 'nullable|numeric|min:0',
            'driver_wage' => 'nullable|numeric|min:0',
            'other_expenses' => 'nullable|numeric|min:0',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
