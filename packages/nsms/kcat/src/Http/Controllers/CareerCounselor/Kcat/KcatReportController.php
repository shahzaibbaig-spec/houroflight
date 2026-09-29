<?php

namespace Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat;

use Nsms\Kcat\Http\Controllers\Controller;
use Nsms\Kcat\Http\Requests\Kcat\StoreKcatReportNoteRequest;

use Nsms\Kcat\Models\KcatAttempt;
use Nsms\Kcat\Services\Kcat\KcatReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KcatReportController extends Controller
{
    public function __construct(private readonly KcatReportService $reportService) {}

    public function show(KcatAttempt $attempt): View
    {
        return view('kcat::career-counselor.kcat.reports.show', ['report' => $this->reportService->generateStudentReport($attempt)]);
    }

    public function print(KcatAttempt $attempt): View
    {
        return view('kcat::career-counselor.kcat.reports.print', ['report' => $this->reportService->generateStudentReport($attempt)]);
    }

    public function notes(StoreKcatReportNoteRequest $request, KcatAttempt $attempt): RedirectResponse
    {
        $this->reportService->createCounselorNotes($attempt, $request->validated(), $request->user());
        return back()->with('success', 'KCAT report notes saved.');
    }



    public function recommendations(KcatAttempt $attempt): View
    {
        return $this->show($attempt);
    }

    public function storeOverride(\Illuminate\Http\Request $request, KcatAttempt $attempt): RedirectResponse
    {
        $validated = $request->validate([
            'counselor_override_stream' => ['required', 'string', 'max:255'],
            'counselor_override_reason' => ['required', 'string'],
        ]);

        $this->reportService->overrideRecommendation($attempt, $validated, $request->user());
        return back()->with('success', 'Stream override saved.');
    }
}
