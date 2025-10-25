<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(Invoice::TYPES)],
            'customer_id' => 'required',
            'user_id' => 'required',
            'discount' => 'required|numeric',
            'payment_method' => ['required', Rule::in(Invoice::PAYMENT_METHODS)],
            'date' => 'required|date',
        ];
    }
}
