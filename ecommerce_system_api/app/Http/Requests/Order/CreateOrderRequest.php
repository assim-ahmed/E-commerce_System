<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'address_id' => 'required|exists:addresses,id',
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required' => 'Address ID is required',
            'address_id.exists' => 'Selected address does not exist',
        ];
    }
}