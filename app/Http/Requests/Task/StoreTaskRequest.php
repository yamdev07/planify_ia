<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:5000'],
            'priority'         => ['sometimes', 'in:low,medium,high,urgent'],
            'status'           => ['sometimes', 'in:todo,in_progress,done'],
            'goal_id'          => [
                'nullable',
                Rule::exists('goals', 'id')->where('user_id', $this->user()->id),
            ],
            'scheduled_at'     => ['nullable', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'category'         => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'goal_id.exists' => 'Cet objectif n\'existe pas ou ne vous appartient pas.',
        ];
    }
}
