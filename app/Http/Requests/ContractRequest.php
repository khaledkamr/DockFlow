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
            'service_one' => 'required',
            'container_storage_price' => 'required',
            'container_storage_period' => 'required',
            'service_two' => 'required',
            'move_container_price' => 'required',
            'move_container_count' => 'required',
            'service_three' => 'required',
            'late_fee' => 'required',
            'late_fee_period' => 'required',
            'service_four' => 'required',
            'exchange_container_price' => 'required',
            'exchange_container_count' => 'required',
        ];
    }
}
