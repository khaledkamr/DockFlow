<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->id;
        return [
            'name' => 'required|string|max:255',
            'NID' => ['required', 'string', 'max:14', 'unique:users,NID,' . $id],
            'phone' => 'required|string|max:15',
        ];
    }
}
