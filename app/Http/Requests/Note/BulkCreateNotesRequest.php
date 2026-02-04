<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class BulkCreateNotesRequest extends FormRequest
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
            'notes' => ['required', 'array', 'min:1'],
            'notes.*.title' => ['nullable', 'string', 'max:255'],
            'notes.*.content' => ['required', 'string'],
            'notes.*.category_id' => ['nullable', 'integer'],
            'notes.*.is_pinned' => ['nullable', 'boolean'],
            'notes.*.created_at' => ['nullable', 'date'],
            'notes.*.updated_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'notes.required' => 'Notes array is required',
            'notes.array' => 'Notes must be an array',
            'notes.min' => 'At least one note is required',
            'notes.*.title.max' => 'Note title cannot exceed 255 characters',
            'notes.*.content.required' => 'Each note must have content',
            'notes.*.created_at.date' => 'Created at must be a valid date',
            'notes.*.updated_at.date' => 'Updated at must be a valid date',
        ];
    }
}
