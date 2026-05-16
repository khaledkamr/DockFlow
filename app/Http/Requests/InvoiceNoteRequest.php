<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:credit,debit',
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
