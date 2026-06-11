<?php

namespace App\Http\Controllers;

use App\Models\LocalRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocalRegistrationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:100',
            'apellido'     => 'required|string|max:100',
            'nombre_local' => 'required|string|max:200',
            'direccion'    => 'required|string|max:300',
            'cuit'         => 'required|string|max:20',
            'instagram'    => 'nullable|string|max:100',
            'email'        => 'required|email|max:200',
            'telefono'     => 'required|string|max:20',
            'descripcion'  => 'nullable|string|max:1000',
            'localidad'    => 'required|string|max:100',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
        ]);

        LocalRegistration::create($validated);

        return response()->json(['message' => 'Registro exitoso']);
    }
}
