<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AiLessonPlannerController extends Controller
{
    public function show(): View
    {
        return view('pages.ai-lesson-planner');
    }

    public function generate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_type' => ['required', 'in:single,pbl,custom'],
            'grade' => ['required', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:120'],
            'topic' => ['nullable', 'string', 'max:160'],
            'duration' => ['required', 'string', 'max:40'],
            'language' => ['required', 'string', 'max:40'],
            'objectives' => ['nullable', 'string', 'max:2500'],
            'pbl_subjects' => ['nullable', 'array'],
            'pbl_subjects.*' => ['string', 'max:80'],
            'pbl_theme' => ['nullable', 'string', 'max:180'],
            'custom_files' => ['nullable', 'array'],
            'custom_files.*.name' => ['required_with:custom_files', 'string', 'max:120'],
            'custom_files.*.type' => ['required_with:custom_files', 'in:text,image'],
            'custom_files.*.mime_type' => ['nullable', 'string', 'max:80'],
            'custom_files.*.content' => ['required_with:custom_files', 'string'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $type = $request->string('plan_type')->toString();
            if ($type === 'single') {
                if (! $request->filled('subject')) {
                    $validator->errors()->add('subject', 'Subject is required for single lesson plan.');
                }
                if (! $request->filled('topic')) {
                    $validator->errors()->add('topic', 'Topic is required for single lesson plan.');
                }
            }
            if ($type === 'pbl') {
                if (! is_array($request->input('pbl_subjects')) || count($request->input('pbl_subjects')) === 0) {
                    $validator->errors()->add('pbl_subjects', 'Select at least one PBL subject.');
                }
                if (! $request->filled('pbl_theme')) {
                    $validator->errors()->add('pbl_theme', 'PBL theme is required.');
                }
            }
            if ($type === 'custom') {
                if (! is_array($request->input('custom_files')) || count($request->input('custom_files')) === 0) {
                    $validator->errors()->add('custom_files', 'Upload at least one custom file.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $parts = $this->buildLessonPlanParts($validated);
            $response = $this->callGemini($parts);
            $text = $this->extractText($response->json());

            if ($text === '') {
                return response()->json([
                    'message' => 'Gemini returned an empty response.',
                ], 500);
            }

            return response()->json([
                'lesson_plan' => $text,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function generateQuiz(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lesson_plan' => ['required', 'string', 'max:30000'],
            'quiz_type' => ['required', 'in:Multiple Choice,True / False,Fill in the Blanks,Short Answer,Mixed'],
        ]);

        $prompt = Str::of("
You are an expert educator. Generate a {$validated['quiz_type']} quiz from this lesson plan.

Requirements:
- 5 to 7 questions
- Add clear instructions for students
- Include an Answer Key section at the end
- Use markdown formatting

Lesson plan:
{$validated['lesson_plan']}
")->trim()->toString();

        try {
            $response = $this->callGemini([['text' => $prompt]]);
            $text = $this->extractText($response->json());

            if ($text === '') {
                return response()->json(['message' => 'Gemini returned an empty quiz response.'], 500);
            }

            return response()->json(['quiz' => $text]);
        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function generatePresentation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lesson_plan' => ['required', 'string', 'max:30000'],
            'include_images' => ['nullable', 'boolean'],
        ]);

        $withImages = (bool) ($validated['include_images'] ?? false);
        $imageRule = $withImages
            ? "Include image_prompt for each slide with stock-photo style prompt."
            : "Do not include image_prompt field.";

        $prompt = Str::of("
Create a JSON array for a lesson presentation with 6 to 8 slides.

Fields for each slide:
- slide_number (integer)
- title (string)
- content (string, use newline-separated bullet style text)
".($withImages ? "- image_prompt (string)\n" : '')."
Rules:
- Keep language teacher-friendly.
- Make slide flow logical from intro to recap.
- {$imageRule}
- Return ONLY valid JSON array. No markdown.

Lesson plan:
{$validated['lesson_plan']}
")->trim()->toString();

        try {
            $response = $this->callGemini([['text' => $prompt]]);
            $text = $this->extractText($response->json());
            $decoded = json_decode($text, true);

            if (! is_array($decoded)) {
                return response()->json([
                    'message' => 'Gemini did not return valid presentation JSON.',
                    'raw' => $text,
                ], 500);
            }

            return response()->json([
                'slides' => $decoded,
            ]);
        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    protected function buildLessonPlanParts(array $data): array
    {
        $type = $data['plan_type'];
        $objectives = trim((string) ($data['objectives'] ?? ''));

        if ($type === 'pbl') {
            $goalBlock = $objectives !== ''
                ? "Project goals provided by teacher:\n{$objectives}"
                : 'Project goals: infer practical goals from theme and subjects.';

            $prompt = Str::of("
You are an expert in Project-Based Learning.
Create a complete PBL unit using clean HTML tables only.

Inputs:
- Grade: {$data['grade']}
- Integrated Subjects: ".implode(', ', $data['pbl_subjects'] ?? [])."
- Theme/Driving Question: {$data['pbl_theme']}
- Duration: {$data['duration']}
- Language: {$data['language']}

{$goalBlock}

Required HTML output format:
- First table columns: Section | Details
- Include rows for: Project Title, Driving Question, Learning Goals, Entry Event, Final Product/Exhibition, Assessment Rubric
- Second table title: Milestones and Timeline
- Second table columns: Phase | Duration | Activities | Output
- Third table title: Subject Integration
- Third table columns: Subject | Activity | Learning Evidence
- Timeline rules:
  - Break project into realistic sequential phases with practical durations.
  - Durations must add up logically to the given project duration.
  - Include at least one milestone for planning, implementation, and reflection.
- Do not wrap in markdown code fences.
")->trim()->toString();

            return [['text' => $prompt]];
        }

        if ($type === 'custom') {
            $goalBlock = $objectives !== ''
                ? "Teacher objectives:\n{$objectives}"
                : 'Teacher objectives: infer from uploaded content.';

            $parts = [[
                'text' => Str::of("
You are an expert lesson planner.
Create a full lesson plan based on uploaded files using clean HTML tables only.

Inputs:
- Grade: {$data['grade']}
- Duration: {$data['duration']}
- Language: {$data['language']}
- {$goalBlock}

Required HTML output format:
- First table columns: Section | Details
- Include rows for: Topic, Learning Objectives, Materials Needed, Interactive Activity, Assessment, Homework
- Second table title: Lesson Procedure
- Second table columns: Time | Segment | Teacher Actions | Student Actions | Resources
- Timing rules for Lesson Procedure:
  - Use realistic classroom pacing with 5 to 7 segments.
  - Time values must be in minutes (e.g., 5 min, 8 min, 12 min).
  - Segment timings must add up exactly to the requested duration.
  - Avoid any segment shorter than 3 minutes.
- Do not wrap in markdown code fences.
")->trim()->toString(),
            ]];

            foreach ($data['custom_files'] ?? [] as $file) {
                if ($file['type'] === 'text') {
                    $parts[] = [
                        'text' => "\n\nUploaded text file: {$file['name']}\n{$file['content']}",
                    ];
                    continue;
                }

                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $file['mime_type'] ?: 'image/png',
                        'data' => $this->extractBase64Data($file['content']),
                    ],
                ];
            }

            return $parts;
        }

        $objectiveBlock = $objectives !== ''
            ? "Teacher objectives:\n{$objectives}\n"
            : 'Teacher objectives: infer practical lesson objectives for this grade and topic.'."\n";

        $prompt = Str::of("
You are an expert school teacher and curriculum planner.
Create a complete, classroom-ready lesson plan using clean HTML tables only.

Inputs:
- Grade: {$data['grade']}
- Subject: {$data['subject']}
- Topic: {$data['topic']}
- Duration: {$data['duration']}
- Language: {$data['language']}

{$objectiveBlock}

Required HTML output format:
- First table columns: Section | Details
- Include rows for: Lesson Title, Learning Objectives, Materials Needed, Differentiation (Support), Differentiation (Challenge), Assessment, Homework/Extension
- Second table title: Lesson Flow
- Second table columns: Time | Segment | Teacher Actions | Student Actions | Assessment Check
- Timing rules for Lesson Flow:
  - Use realistic pacing with 5 to 7 segments.
  - Time values must be in minutes (e.g., 5 min, 8 min, 12 min).
  - Segment timings must add up exactly to the requested duration.
  - Avoid any segment shorter than 3 minutes.
- Do not wrap in markdown code fences.
")->trim()->toString();

        return [['text' => $prompt]];
    }

    protected function callGemini(array $parts)
    {
        $apiKey = (string) config('services.gemini.api_key');
        if ($apiKey === '') {
            throw new \RuntimeException('Gemini API key is not configured. Set GEMINI_API_KEY in .env.');
        }

        $configuredModel = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $candidateModels = array_values(array_unique([
            $configuredModel,
            'gemini-2.5-flash',
            'gemini-2.0-flash',
            'gemini-flash-latest',
            'gemini-1.5-flash-latest',
        ]));

        $lastError = 'Gemini request failed.';
        foreach ($candidateModels as $model) {
            $response = Http::timeout(90)
                // Force direct HTTPS call to Gemini and ignore system proxy env vars.
                ->withOptions([
                    'proxy' => [
                        'http' => null,
                        'https' => null,
                        'no' => ['*'],
                    ],
                ])
                ->acceptJson()
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    [
                        'contents' => [
                            [
                                'parts' => $parts,
                            ],
                        ],
                    ]
                );

            if ($response->successful()) {
                return $response;
            }

            $lastError = (string) data_get($response->json(), 'error.message', 'Gemini request failed.');
            $notFound = str_contains(strtolower($lastError), 'not found')
                || str_contains(strtolower($lastError), 'not supported');
            if (! $notFound) {
                break;
            }
        }

        throw new \RuntimeException($lastError);
    }

    protected function extractText(array $payload): string
    {
        $parts = data_get($payload, 'candidates.0.content.parts', []);
        if (! is_array($parts)) {
            return '';
        }

        $text = '';
        foreach ($parts as $part) {
            $value = $part['text'] ?? null;
            if (is_string($value)) {
                $text .= $value;
            }
        }

        return trim($text);
    }

    protected function extractBase64Data(string $value): string
    {
        if (str_contains($value, ',')) {
            $chunks = explode(',', $value, 2);
            return $chunks[1] ?? $value;
        }

        return $value;
    }
}
