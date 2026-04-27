<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'           => ['sometimes', 'string', 'max:255'],
            'start_at'        => ['sometimes', 'date'],
            'end_at'          => ['sometimes', 'date', 'after:start_at'],
            'is_recurring'    => ['sometimes', 'boolean'],
            'recurrence_rule' => ['nullable', 'string', 'max:500'],
            'color'           => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
