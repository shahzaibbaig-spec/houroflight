<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVolunteerProfileRequest extends FormRequest
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
            'expertise_subjects' => ['required', 'string', 'max:2000'],
            'grade_levels' => ['required', 'string', 'max:255'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:80'],
            'availability' => ['nullable', 'string', 'max:255'],
            'lesson_format' => ['nullable', Rule::in(['recorded', 'live', 'both'])],
            'short_bio' => ['nullable', 'string', 'max:4000'],
            'teaching_profile_notes' => ['nullable', 'string', 'max:4000'],
            'degree_details' => ['nullable', 'string', 'max:3000'],
            'awards' => ['nullable', 'string', 'max:3000'],
            'show_photo_on_website' => ['nullable', 'boolean'],
            'show_on_website' => ['nullable', 'boolean'],
            'profile_photo' => ['nullable', 'image', 'max:5120'],
            'degree_document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:25600'],
            'certificates_document' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:25600'],
            'certificates_documents' => ['nullable', 'array', 'max:20'],
            'certificates_documents.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:25600'],
        ];
    }
}
