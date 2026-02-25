<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVolunteerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'is_volunteer_teacher' => ['nullable', 'boolean'],
            'expertise_subjects' => ['required_if:is_volunteer_teacher,1', 'nullable', 'string', 'max:2000'],
            'grade_levels' => ['required_if:is_volunteer_teacher,1', 'nullable', 'string', 'max:255'],
            'years_experience' => ['required_if:is_volunteer_teacher,1', 'nullable', 'integer', 'min:0', 'max:80'],
            'lesson_format' => ['required_if:is_volunteer_teacher,1', 'nullable', Rule::in(['recorded', 'live', 'both'])],
            'availability' => ['required_if:is_volunteer_teacher,1', 'nullable', 'string', 'max:255'],
            'short_bio' => ['required_if:is_volunteer_teacher,1', 'nullable', 'string', 'max:3000'],
        ];
    }
}

