<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'expected_end_date' => 'required|date|after:start_date',
            'late_fee' => 'required|numeric|min:0',
            'tax' => 'required|in:معفي,غير معفي',
        ];
    }
}
