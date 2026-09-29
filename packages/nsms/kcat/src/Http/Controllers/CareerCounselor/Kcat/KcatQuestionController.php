<?php

namespace Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat;

use Nsms\Kcat\Http\Controllers\Controller;
use Nsms\Kcat\Http\Requests\Kcat\StoreKcatQuestionRequest;
use Nsms\Kcat\Models\KcatQuestion;
use Nsms\Kcat\Models\KcatSection;
use Nsms\Kcat\Models\KcatTest;
use Nsms\Kcat\Services\Kcat\KcatTestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KcatQuestionController extends Controller
{
    public function __construct(private readonly KcatTestService $testService) {}

    public function create(KcatTest $test): View
    {
        return view('kcat::career-counselor.kcat.questions.create', ['test' => $test->load('sections'), 'question' => null]);
    }

    public function store(StoreKcatQuestionRequest $request): RedirectResponse
    {
        $section = KcatSection::query()->with('test')->findOrFail((int) $request->validated('kcat_section_id'));
        $question = $this->testService->addQuestion($section, $request->validated(), $request->user());
        return redirect()->route('kcat.career-counselor.kcat.tests.show', $question->kcat_test_id)->with('success', 'KCAT question added.');
    }

    public function edit(KcatQuestion $question): View
    {
        return view('kcat::career-counselor.kcat.questions.edit', ['question' => $question->load(['options', 'test.sections']), 'test' => $question->test]);
    }

    public function update(StoreKcatQuestionRequest $request, KcatQuestion $question): RedirectResponse
    {
        $this->testService->updateQuestion($question, $request->validated(), $request->user());
        return redirect()->route('kcat.career-counselor.kcat.tests.show', $question->kcat_test_id)->with('success', 'KCAT question updated.');
    }

    public function destroy(KcatQuestion $question): RedirectResponse
    {
        $question->update(['is_active' => false]);
        $this->testService->refreshTotals($question->test);
        return back()->with('success', 'KCAT question deactivated.');
    }
}
