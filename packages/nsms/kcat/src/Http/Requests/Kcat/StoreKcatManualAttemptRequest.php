<?php

namespace Nsms\Kcat\Http\Requests\Kcat;

use Illuminate\Foundation\Http\FormRequest;

class StoreKcatManualAttemptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('kcat.manually_enter_kcat_attempt');
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:kcat_students,id'],
            'answers' => ['required', 'array'],
        ];
    }
}
