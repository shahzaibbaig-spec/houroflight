<?php

namespace Nsms\Kcat\Http\Controllers\Principal\Kcat;

use Nsms\Kcat\Http\Controllers\Controller;
use Nsms\Kcat\Models\KcatQuestion;
use Nsms\Kcat\Models\KcatSection;
use Nsms\Kcat\Services\Kcat\KcatQuestionQualityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KcatQuestionQualityController extends Controller
{
    public function __construct(private readonly KcatQuestionQualityService $qualityService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['section_id', 'difficulty', 'review_status', 'discrimination_flag', 'only_flagged']);

        return view('kcat::principal.kcat.question-quality.index', [
            'questions' => $this->qualityService->flagWeakQuestions($filters),
            'sections' => KcatSection::query()->orderBy('sort_order')->orderBy('name')->get(),
            'summary' => $this->qualityService->reviewStatsSummary(),
            'filters' => $filters,
        ]);
    }

    public function show(KcatQuestion $question): View
    {
        return view('kcat::principal.kcat.question-quality.show', [
            'question' => $question->load(['section', 'test', 'options', 'reviews.reviewer']),
            'analysis' => $this->qualityService->analyzeQuestion($question),
        ]);
    }
}
