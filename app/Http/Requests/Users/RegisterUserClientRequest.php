<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'birth_date' => 'required|date_format:Y-m-d',
            'gender'     => 'required|in:male,female,other',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'El nombre es obligatorio.',
            'name.max'               => 'El nombre no puede superar los 255 caracteres.',
            'last_name.required'     => 'El apellido es obligatorio.',
            'last_name.max'          => 'El apellido no puede superar los 255 caracteres.',
            'phone.required'         => 'El teléfono es obligatorio.',
            'birth_date.required'    => 'La fecha de nacimiento es obligatoria.',
            'birth_date.date_format' => 'La fecha de nacimiento debe tener el formato AAAA-MM-DD.',
            'gender.required'        => 'El género es obligatorio.',
            'gender.in'              => 'El género debe ser male, female u other.',
            'email.required'         => 'El correo electrónico es obligatorio.',
            'email.email'            => 'El correo electrónico no tiene un formato válido.',
            'email.unique'           => 'Ese correo electrónico ya está registrado.',
            'password.required'      => 'La contraseña es obligatoria.',
            'password.min'           => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'     => 'Las contraseñas no coinciden.',
        ];
    }
}