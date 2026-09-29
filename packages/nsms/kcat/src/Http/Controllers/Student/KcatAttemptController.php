<?php

namespace Nsms\Kcat\Http\Controllers\Student;

use Nsms\Kcat\Http\Controllers\Controller;
use Nsms\Kcat\Models\KcatAssignment;
use Nsms\Kcat\Models\KcatAttempt;
use Nsms\Kcat\Models\KcatQuestion;
use Nsms\Kcat\Models\Student;
use Nsms\Kcat\Services\StudentResolver;
use Nsms\Kcat\Services\Kcat\KcatAttemptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KcatAttemptController extends Controller
{
    public function __construct(
        private readonly KcatAttemptService $attemptService,
        private readonly StudentResolver $studentResolver,
    ) {}

    public function index(Request $request): View
    {
        $student = $this->studentForUser($request);
        return view('kcat::student.kcat.assignments.index', [
            'assignments' => KcatAssignment::query()
                ->with(['test', 'attempts' => fn ($query) => $query
                    ->where('student_id', $student->id)->latest('id')])
                ->where(function ($query) use ($student): void {
                    $query->where(fn ($individual) => $individual
                        ->where('assigned_to_type', 'student')->where('student_id', $student->id));
                    if ($student->class_id !== null) {
                        $query->orWhere(fn ($class) => $class
                            ->where('assigned_to_type', 'class')->where('class_id', $student->class_id));
                    }
                })
                ->latest('assigned_at')
                ->get(),
        ]);
    }

    public function start(Request $request, KcatAssignment $assignment): RedirectResponse
    {
        $assignment->loadMissing('test');
        $hasActiveQuestions = $assignment->test
            ? $assignment->test->questions()->where('is_active', true)->whereNull('retired_at')->exists()
            : false;

        if (! $hasActiveQuestions) {
            return back()->with('error', 'This KCAT assignment has no active questions yet. Please contact your teacher/counselor.');
        }

        $attempt = $this->attemptService->startAttempt($assignment, $this->studentForUser($request));
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('kcat.student.kcat.attempts.result', $attempt);
        }
        if ($attempt->is_adaptive) {
            return redirect()->route('kcat.student.kcat.attempts.adaptive.next', $attempt);
        }

        return redirect()->route('kcat.student.kcat.attempts.question', [$attempt, 0]);
    }

    public function question(KcatAttempt $attempt, int $index = 0): View
    {
        abort_unless((int) $attempt->student_id === (int) $this->studentForUser(request())->id, 403);
        abort_unless($attempt->status === 'in_progress', 403);
        if ($attempt->is_adaptive) {
            abort(404);
        }
        $questions = $attempt->test->questions()->where('is_active', true)->whereNull('retired_at')->with('options')->get();
        return view('kcat::student.kcat.attempts.question', ['attempt' => $attempt, 'questions' => $questions, 'question' => $questions->get($index), 'index' => $index]);
    }

    public function answer(Request $request, KcatAttempt $attempt, KcatQuestion $question): RedirectResponse
    {
        abort_unless((int) $attempt->student_id === (int) $this->studentForUser($request)->id, 403);
        if ($attempt->is_adaptive) {
            abort(404);
        }
        $validated = $request->validate([
            'selected_option_id' => ['nullable', 'exists:kcat_question_options,id'],
            'answer_text' => ['nullable', 'string'],
            'response_time_seconds' => ['nullable', 'integer', 'min:0', 'max:3600'],
            'next_index' => ['nullable', 'integer'],
        ]);
        $this->attemptService->saveAnswer($attempt, $question, $validated);
        return redirect()->route('kcat.student.kcat.attempts.question', [$attempt, max(0, min((int) ($validated['next_index'] ?? 0), $attempt->test->questions()->where('is_active', true)->whereNull('retired_at')->count() - 1))]);
    }

    public function submit(Request $request, KcatAttempt $attempt): RedirectResponse
    {
        abort_unless((int) $attempt->student_id === (int) $this->studentForUser($request)->id, 403);
        $this->attemptService->submitAttempt($attempt);
        return redirect()->route('kcat.student.kcat.attempts.result', $attempt);
    }

    public function result(Request $request, KcatAttempt $attempt): View
    {
        abort_unless((int) $attempt->student_id === (int) $this->studentForUser($request)->id, 403);
        abort_unless(in_array($attempt->status, ['submitted', 'reviewed'], true), 403, 'Complete your test to view its report.');
        return view('kcat::student.kcat.attempts.result', ['attempt' => $attempt->load(['test', 'scores.section', 'latestReportNote', 'streamRecommendations'])]);
    }

    public function adaptiveQuestion(Request $request, KcatAttempt $attempt): View|RedirectResponse
    {
        abort_unless((int) $attempt->student_id === (int) $this->studentForUser($request)->id, 403);
        abort_unless($attempt->status === 'in_progress', 403);
        abort_unless($attempt->is_adaptive, 404);

        $question = $this->attemptService->getNextAdaptiveQuestion($attempt->fresh(['test.sections', 'answers']) ?? $attempt);
        if (! $question) {
            $this->attemptService->submitAttempt($attempt);
            return redirect()->route('kcat.student.kcat.attempts.result', $attempt);
        }
        $question->loadMissing('section');

        $attempt = $attempt->fresh(['test.sections', 'answers']) ?? $attempt;
        $state = $attempt->adaptive_state ?? [];
        $requiredPerSection = max((int) ($attempt->test?->questions_per_section ?? 10), 1);
        $currentSection = $question->section;
        $currentSectionCode = $currentSection?->code;
        $sectionState = $currentSectionCode ? ($state['sections'][$currentSectionCode] ?? []) : [];
        $sectionAnswered = (int) ($sectionState['answered'] ?? 0);
        $totalAnswered = collect($state['sections'] ?? [])->sum(fn (array $row): int => (int) ($row['answered'] ?? 0));
        $totalRequired = $requiredPerSection * max((int) $attempt->test?->sections?->count(), 1);

        return view('kcat::student.kcat.attempts.adaptive-question', [
            'attempt' => $attempt,
            'question' => $question->load('options', 'section'),
            'requiredPerSection' => $requiredPerSection,
            'sectionAnswered' => $sectionAnswered,
            'totalAnswered' => $totalAnswered,
            'totalRequired' => $totalRequired,
        ]);
    }

    public function adaptiveAnswer(Request $request, KcatAttempt $attempt): RedirectResponse
    {
        abort_unless((int) $attempt->student_id === (int) $this->studentForUser($request)->id, 403);
        abort_unless($attempt->is_adaptive, 404);

        $validated = $request->validate([
            'selected_option_id' => ['nullable', 'exists:kcat_question_options,id'],
            'answer_text' => ['nullable', 'string'],
            'response_time_seconds' => ['nullable', 'integer', 'min:0', 'max:3600'],
        ]);

        $result = $this->attemptService->saveAdaptiveAnswer($attempt, $validated);
        if (($result['completed'] ?? false) === true) {
            return redirect()->route('kcat.student.kcat.attempts.result', $attempt);
        }

        return redirect()->route('kcat.student.kcat.attempts.adaptive.next', $attempt);
    }

    private function studentForUser(Request $request): Student
    {
        $student = $this->studentResolver->resolveStudentForUser($request->user());
        abort_unless($student, 403);

        return $student;
    }
}
