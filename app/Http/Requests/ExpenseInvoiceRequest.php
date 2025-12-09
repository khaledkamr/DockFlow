<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_invoice_number' => 'nullable|string|max:255',
            'payment_method' => 'required|string|max:50',
            'date' => 'required|date',
            'amount_before_tax' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount_rate' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'expense_account_id' => 'nullable|exists:accounts,id',
        ];
    }
}
