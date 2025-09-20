<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required',
            'user_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'payment_method' => 'required'
        ];
    }
}
