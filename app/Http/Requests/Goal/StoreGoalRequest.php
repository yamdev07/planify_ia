<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'deadline'    => ['nullable', 'date', 'after:today'],
            'status'      => ['sometimes', 'in:active,completed,abandoned'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'   => 'Le titre est obligatoire.',
            'deadline.after'   => 'La deadline doit être dans le futur.',
        ];
    }
}
