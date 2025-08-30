<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required', 
            'date' => 'required|date',
            'made_by' => 'nullable',
            'modified_by' => 'nullable',
            'voucher_id' => 'nullable',
        ];
    }
}
