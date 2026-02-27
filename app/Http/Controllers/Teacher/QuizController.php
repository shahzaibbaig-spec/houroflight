<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\QuizAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function take(Course $course): View
    {
        abort_unless($course->is_active, 404);

        $quiz = $course->quizzes()
            ->with([
                'questions.options',
                'attempts' => fn ($query) => $query
                    ->where('user_id', (int) auth()->id())
                    ->latest('attempted_at'),
            ])
            ->firstOrFail();

        $certificate = Certificate::query()
            ->where('user_id', (int) auth()->id())
            ->where('course_id', (int) $course->id)
            ->first();

        return view('teacher.quiz.take', [
            'course' => $course,
            'quiz' => $quiz,
            'latestAttempt' => $quiz->attempts->first(),
            'certificate' => $certificate,
        ]);
    }

    public function submit(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->is_active, 404);

        $quiz = $course->quizzes()
            ->with('questions.options')
            ->firstOrFail();

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable', 'integer'],
        ]);

        $scoreMarks = 0;
        foreach ($quiz->questions as $question) {
            $selectedOptionId = (int) data_get($validated, "answers.{$question->id}", 0);
            if ($selectedOptionId === 0) {
                continue;
            }

            $isCorrect = $question->options->firstWhere('id', $selectedOptionId)?->is_correct;
            if ($isCorrect) {
                $scoreMarks += (int) $question->marks;
            }
        }

        $totalMarks = max((int) $quiz->total_marks, 1);
        $percentageScore = (int) round(($scoreMarks / $totalMarks) * 100);
        $passed = $percentageScore >= (int) $course->passing_score;

        DB::transaction(function () use ($request, $course, $quiz, $percentageScore, $passed): void {
            QuizAttempt::create([
                'user_id' => (int) $request->user()->id,
                'quiz_id' => (int) $quiz->id,
                'score' => $percentageScore,
                'passed' => $passed,
                'attempted_at' => now(),
            ]);

            if (! $passed) {
                return;
            }

            $certificate = Certificate::firstOrNew([
                'user_id' => (int) $request->user()->id,
                'course_id' => (int) $course->id,
            ]);

            if (! $certificate->exists) {
                $certificate->certificate_number = $this->generateCertificateNumber(
                    (int) $course->id,
                    (int) $request->user()->id
                );
            }

            $certificate->issued_at = now();
            $certificate->save();

            $pdfPath = $this->storeCertificatePdf(
                $certificate,
                $course->title,
                (string) $request->user()->name
            );

            $certificate->update([
                'pdf_path' => $pdfPath,
            ]);
        });

        return redirect()
            ->route('teacher.courses.quiz.take', $course)
            ->with('success', $passed
                ? "Quiz submitted. You passed with {$percentageScore}% and your certificate has been generated."
                : "Quiz submitted. You scored {$percentageScore}%."
            );
    }

    protected function generateCertificateNumber(int $courseId, int $userId): string
    {
        $tail = str_pad((string) $courseId, 2, '0', STR_PAD_LEFT)
            .str_pad((string) $userId, 2, '0', STR_PAD_LEFT);

        return 'HOL-CL-'.now()->format('Y').'-'.$tail;
    }

    protected function storeCertificatePdf(Certificate $certificate, string $courseTitle, string $teacherName): string
    {
        $issuedAt = $certificate->issued_at instanceof Carbon
            ? $certificate->issued_at
            : Carbon::parse($certificate->issued_at);

        $pdf = Pdf::loadView('pdf.course_certificate', [
            'teacherName' => $teacherName,
            'courseTitle' => $courseTitle,
            'issueDate' => $issuedAt->format('F d, Y'),
            'certificateNumber' => $certificate->certificate_number,
        ])->setPaper('a4', 'landscape');

        $fileName = 'certificates/'.$certificate->certificate_number.'.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        return $fileName;
    }
}
