<?php

namespace App\Http\Requests\Payments;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreatePaymentRequest extends FormRequest
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
            'payment_method' => 'required|string',
            'booking_id'     => 'required|exists:bookings,id',
            'platform'       => 'sometimes|string|in:web,native',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'El método de pago es obligatorio.',
            'payment_method.string'   => 'El método de pago debe ser una cadena de texto.',
            'booking_id.required'     => 'El ID de la reserva es obligatorio.',
            'booking_id.exists'       => 'El ID de la reserva debe existir en la base de datos.',
            'platform.in'             => 'La plataforma debe ser "web" o "native".',
        ];
    }
}
