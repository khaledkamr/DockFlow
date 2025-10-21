<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_id' => 'required|exists:transactions,id',
            'customer_id' => 'required|exists:customers,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'date' => 'required|date',
            'from' => 'required|string',
            'to' => 'required|string',
            'duration' => 'nullable|string',
            'notes' => 'nullable|string',
            'diesel_cost' => 'nullable|numeric|min:0',
            'driver_wage' => 'nullable|numeric|min:0',
            'other_expenses' => 'nullable|numeric|min:0',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
