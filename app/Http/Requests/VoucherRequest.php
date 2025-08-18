<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required',
            'type' => 'required',
            'date' => 'required|date',
            'amount' => 'required',
            'hatching' => 'required|string',
            'description' => 'required|string'
        ];
    }
}
