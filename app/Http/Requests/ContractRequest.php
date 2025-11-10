<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required',
            'end_date' => 'required',
            'user_id' => 'nullable|exists:users,id',
            'company_id' => 'required',
            'company_representative' => 'required',
            'company_representative_nationality' => 'required',
            'company_representative_NID' => 'required',
            'company_representative_role' => 'required',
            'customer_id' => 'required',
            'customer_representative' => 'required',
            'customer_representative_nationality' => 'required',
            'customer_representative_NID' => 'required',
            'customer_representative_role' => 'required',
            'payment_grace_period' => 'required|integer',
            'payment_grace_period_unit' => 'required|string',
        ];
    }
}
