<?php

namespace Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat;

use Nsms\Kcat\Http\Controllers\Controller;
use Nsms\Kcat\Http\Requests\Kcat\StoreKcatQuestionReviewRequest;
use Nsms\Kcat\Models\KcatQuestion;
use Nsms\Kcat\Models\KcatSection;
use Nsms\Kcat\Services\Kcat\KcatQuestionQualityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KcatQuestionQualityController extends Controller
{
    public function __construct(private readonly KcatQuestionQualityService $qualityService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['section_id', 'difficulty', 'review_status', 'discrimination_flag', 'only_flagged']);

        return view('kcat::career-counselor.kcat.question-quality.index', [
            'questions' => $this->qualityService->flagWeakQuestions($filters),
            'sections' => KcatSection::query()->orderBy('sort_order')->orderBy('name')->get(),
            'summary' => $this->qualityService->reviewStatsSummary(),
            'filters' => $filters,
        ]);
    }

    public function show(KcatQuestion $question): View
    {
        return view('kcat::career-counselor.kcat.question-quality.show', [
            'question' => $question->load(['section', 'test', 'options', 'reviews.reviewer']),
            'analysis' => $this->qualityService->analyzeQuestion($question),
        ]);
    }

    public function storeReview(StoreKcatQuestionReviewRequest $request, KcatQuestion $question): RedirectResponse
    {
        $this->qualityService->submitReview($question, $request->validated(), $request->user());
        return back()->with('success', 'Question review saved.');
    }

    public function approve(Request $request, KcatQuestion $question): RedirectResponse
    {
        $this->qualityService->approveQuestion($question, $request->user());
        return back()->with('success', 'Question approved.');
    }

    public function retire(Request $request, KcatQuestion $question): RedirectResponse
    {
        $this->qualityService->retireQuestion($question, $request->user());
        return back()->with('success', 'Question retired. It will remain linked with previous attempts.');
    }
}
