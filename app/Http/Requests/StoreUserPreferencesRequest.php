<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPreferencesRequest extends FormRequest
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
            'preferred_sources' => 'array|nullable',
            'preferred_categories' => 'array|nullable',
            'preferred_authors' => 'array|nullable',
        ];
    }

    /**
     * Get Custom Validation Error Messages
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'preferred_sources.array' => 'Preferred sources must be an array.',
            'preferred_categories.array' => 'Preferred categories must be an array.',
            'preferred_authors.array' => 'Preferred authors must be an array.',
        ];
    }

}
