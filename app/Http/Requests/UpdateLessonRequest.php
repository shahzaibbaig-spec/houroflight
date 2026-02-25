<?php

namespace App\Http\Requests;

use App\Models\Lesson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLessonRequest extends FormRequest
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
        /** @var Lesson|null $lesson */
        $lesson = $this->route('lesson');
        $selectedMode = (string) $this->input('delivery_mode');

        $requiresYoutube = $selectedMode === 'youtube_link'
            && (! $lesson || $lesson->delivery_mode !== 'youtube_link' || empty($lesson->youtube_url));
        $requiresVideo = $selectedMode === 'video_upload'
            && (! $lesson || $lesson->delivery_mode !== 'video_upload' || empty($lesson->video_path));
        $requiresDocument = $selectedMode === 'document_upload'
            && (! $lesson || $lesson->delivery_mode !== 'document_upload' || empty($lesson->document_path));

        return [
            'title' => ['required', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:120'],
            'grade_min' => ['required', 'integer', 'min:1', 'max:12'],
            'grade_max' => ['required', 'integer', 'min:1', 'max:12', 'gte:grade_min'],
            'lesson_type' => ['required', Rule::in(['recorded', 'live', 'worksheet'])],
            'delivery_mode' => ['required', Rule::in(['video_upload', 'youtube_link', 'document_upload'])],
            'youtube_url' => ['nullable', Rule::requiredIf($requiresYoutube), 'url', 'max:1000'],
            'video' => ['nullable', Rule::requiredIf($requiresVideo), 'file', 'mimetypes:video/mp4,video/quicktime', 'max:204800'],
            'document' => ['nullable', Rule::requiredIf($requiresDocument), 'file', 'mimes:pdf,doc,docx', 'max:25600'],
            'description' => ['required', 'string', 'min:30'],
            'learning_objectives' => ['nullable', 'string'],
            'language' => ['required', Rule::in(['English', 'Urdu'])],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:240'],
            // Backward-compatible aliases for existing form field names.
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime', 'max:204800'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:25600'],
        ];
    }
}
