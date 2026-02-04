<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteNotesRequest extends FormRequest
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
            'note_ids' => ['required', 'array', 'min:1'],
            'note_ids.*' => ['required', 'integer', 'exists:notes,id'],
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
            'note_ids.required' => 'Note IDs array is required',
            'note_ids.array' => 'Note IDs must be an array',
            'note_ids.min' => 'At least one note ID is required',
            'note_ids.*.required' => 'Each element must be a note ID',
            'note_ids.*.integer' => 'Each note ID must be an integer',
            'note_ids.*.exists' => 'One or more note IDs do not exist',
        ];
    }
}
