<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $this->user()->id,
            'current_password' => 'nullable|string',
            'password'         => 'nullable|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre es obligatorio.',
            'name.max'           => 'El nombre no puede superar los 255 caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El correo electrónico no tiene un formato válido.',
            'email.unique'       => 'Ese correo electrónico ya está registrado por otra cuenta.',
            'password.min'       => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('password')) {
                if (!$this->filled('current_password')) {
                    $validator->errors()->add(
                        'current_password',
                        'Debés ingresar tu contraseña actual para poder cambiarla.'
                    );
                    return;
                }
                if (!Hash::check($this->current_password, $this->user()->password)) {
                    $validator->errors()->add(
                        'current_password',
                        'La contraseña actual es incorrecta.'
                    );
                }
            }
        });
    }
}
