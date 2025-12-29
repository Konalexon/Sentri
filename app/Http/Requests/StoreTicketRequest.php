<?php

namespace App\Http\Requests;

use App\Enums\TicketPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // All authenticated users can create tickets
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:5', 'max:10000'],
            'priority' => ['sometimes', Rule::enum(TicketPriority::class)],
            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'Temat zgłoszenia jest wymagany.',
            'subject.min' => 'Temat musi mieć co najmniej :min znaków.',
            'description.required' => 'Opis problemu jest wymagany.',
            'description.min' => 'Opis musi mieć co najmniej :min znaków.',
            'priority.enum' => 'Nieprawidłowy priorytet zgłoszenia.',
            'tag_ids.*.exists' => 'Wybrany tag nie istnieje.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'subject' => 'temat',
            'description' => 'opis',
            'priority' => 'priorytet',
            'tag_ids' => 'tagi',
        ];
    }
}
