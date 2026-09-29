<?php

namespace Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat;

use Nsms\Kcat\Http\Controllers\Controller;
use Nsms\Kcat\Http\Requests\Kcat\StoreKcatManualAttemptRequest;
use Nsms\Kcat\Models\KcatAttempt;
use Nsms\Kcat\Models\KcatTest;
use Nsms\Kcat\Models\Student;
use Nsms\Kcat\Services\Kcat\KcatAttemptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KcatAttemptController extends Controller
{
    public function __construct(private readonly KcatAttemptService $attemptService) {}

    public function index(): View
    {
        return view('kcat::career-counselor.kcat.assignments.index', [
            'assignments' => collect(),
            'attempts' => KcatAttempt::query()->with(['student.classRoom', 'test'])->latest('submitted_at')->paginate(20),
        ]);
    }

    public function show(KcatAttempt $attempt): RedirectResponse
    {
        return redirect()->route('kcat.career-counselor.kcat.reports.show', $attempt);
    }

    public function destroy(KcatAttempt $attempt): RedirectResponse
    {
        $this->attemptService->deleteResult($attempt);
        return redirect()->route('kcat.career-counselor.kcat.attempts.index')->with('success', 'KCAT result deleted.');
    }

    public function manualEntry(KcatTest $test): View
    {
        return view('kcat::career-counselor.kcat.attempts.manual-entry', [
            'test' => $test->load([
                'sections.questions' => fn ($query) => $query->where('is_active', true)->whereNull('retired_at'),
                'sections.questions.options',
            ]),
        ]);
    }

    public function storeManualEntry(StoreKcatManualAttemptRequest $request, KcatTest $test): RedirectResponse
    {
        $student = Student::query()->findOrFail((int) $request->validated('student_id'));
        $attempt = $this->attemptService->manualEntry($test, $student, $request->validated('answers'), $request->user());
        return redirect()->route('kcat.career-counselor.kcat.reports.show', $attempt)->with('success', 'Manual KCAT attempt scored.');
    }
}
