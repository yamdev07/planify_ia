<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'start_at'        => ['required', 'date'],
            'end_at'          => ['required', 'date', 'after:start_at'],
            'is_recurring'    => ['sometimes', 'boolean'],
            'recurrence_rule' => ['nullable', 'string', 'max:500'],
            'color'           => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_at.after' => 'La fin doit être après le début.',
            'color.regex'  => 'La couleur doit être un code hexadécimal valide (ex: #4f46e5).',
        ];
    }
}
