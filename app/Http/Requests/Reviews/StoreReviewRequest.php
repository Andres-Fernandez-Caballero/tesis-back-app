<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'local_score'     => 'required|integer|min:1|max:5',
            'therapist_score' => 'nullable|integer|min:1|max:5',
            'comment'         => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'local_score.required' => 'La puntuación del local es obligatoria.',
            'local_score.min'      => 'La puntuación debe ser al menos 1.',
            'local_score.max'      => 'La puntuación no puede superar 5.',
        ];
    }
}
