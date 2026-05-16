<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterNotificationTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => [
                'required',
                Rule::in(['ios', 'android', 'web']),
            ],

            'token' => [
                'nullable',
                'string',
                'required_if:platform,ios,android',
            ],

            'endpoint' => [
                'nullable',
                'url',
                'required_if:platform,web',
            ],

            'keys' => [
                'required_if:platform,web',
                'array',
            ],
            'keys.p256dh' => [
                'required_if:platform,web',
                'string',
            ],
            'keys.auth' => [
                'required_if:platform,web',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'platform.required' => 'La plataforma es obligatoria',
            'platform.in' => 'La plataforma debe ser ios, android o web',

            'token.required_if' => 'El token es obligatorio para iOS y Android',

            'endpoint.required_if' => 'El endpoint es obligatorio para Web Push',
            'endpoint.unique' => 'Este navegador ya está registrado',

            'keys.required_if' => 'Las keys son obligatorias para Web Push',
            'keys.p256dh.required_if' => 'La key p256dh es obligatoria',
            'keys.auth.required_if' => 'La key auth es obligatoria',
        ];
    }
}
