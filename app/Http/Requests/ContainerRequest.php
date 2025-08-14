<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContainerRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'container_type_id' => 'required|exists:container_types,id',
            'location' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ];
    }
}
