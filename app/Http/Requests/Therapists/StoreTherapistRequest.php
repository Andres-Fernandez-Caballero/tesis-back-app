<?php

namespace App\Http\Requests\Therapists;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreTherapistRequest extends FormRequest
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
        $type = $this->input('type');

        $base = [
            'type' => ['required', Rule::in(['MassageTherapist', 'OtherTherapist'])],
            'certificate_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'certificate_file_name' => ['required', 'string', 'max:255'],
            'certificate_file_create_date' => ['date'],
            'certificate_file_expiration_date' => [ 'date'],
        ];

        return match ($type) {
            'MassageTherapist' => array_merge($base, [
                'field_m' => ['required', 'string', 'max:255'],
                'field_o' => ['prohibited'], // ❌ No permitido para Car
            ]),
            'OtherTherapist' => array_merge($base, [
                'field_o' => ['required', 'numeric', 'min:100'],
                'field_m' => ['prohibited'], // ❌ No permitido para Truck
            ]),
            default => $base
        };
    }

    protected function failedValidation(Validator $validator)
{
    throw new HttpResponseException(response()->json([
        'message' => 'Validation failed',
        'errors' => $validator->errors()
    ], 422));
}
}
