<?php

namespace App\Http\Requests;

use App\Models\Therapists\Availability;
use Illuminate\Foundation\Http\FormRequest;

class StoreAvailabilityRequest extends FormRequest
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
            'day_of_week'   => ['required', 'array', 'min:1'],
            'day_of_week.*' => ['integer', 'between:1,7'],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'day_of_week.between' => 'day_of_week must be between 1 (Monday) and 7 (Sunday).',
            'end_time.after' => 'end_time must be after start_time.',
        ];
    }

    public function withValidator($validator)
{
    $validator->after(function ($validator) {
        $days = (array) $this->day_of_week;
        $exists = false;
        foreach ($days as $day) {
            $exists = Availability::where('therapist_id', $this->therapist_id)
                ->whereJsonContains('day_of_week', (int) $day)
                ->where('start_time', '<', $this->end_time)
                ->where('end_time', '>', $this->start_time)
                ->exists();
            if ($exists) break;
        }

        if ($exists) {
            $validator->errors()->add(
                'start_time',
                'This availability overlaps with an existing one.'
            );
        }
    });
}

    protected function prepareForValidation(): void
    {
        // Normaliza formatos si vienen raros (ej: 9:00 → 09:00)
        if ($this->start_time) {
            $this->merge([
                'start_time' => date('H:i', strtotime($this->start_time)),
            ]);
        }

        if ($this->end_time) {
            $this->merge([
                'end_time' => date('H:i', strtotime($this->end_time)),
            ]);
        }
    }
}
