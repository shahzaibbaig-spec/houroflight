<?php

namespace Nsms\Kcat\Http\Controllers\Principal\Kcat;

use Nsms\Kcat\Http\Controllers\Controller;
use Nsms\Kcat\Models\KcatAttempt;
use Nsms\Kcat\Models\Student;
use Nsms\Kcat\Services\Kcat\KcatReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KcatReportController extends Controller
{
    public function __construct(private readonly KcatReportService $reportService) {}

    public function index(Request $request): View
    {
        return view('kcat::principal.kcat.reports.index', ['attempts' => $this->reportService->getPrincipalReportData($request->only(['session', 'student']))]);
    }

    public function show(KcatAttempt $attempt): View
    {
        return view('kcat::principal.kcat.reports.show', ['report' => $this->reportService->generateStudentReport($attempt)]);
    }

    public function print(KcatAttempt $attempt): View
    {
        return view('kcat::principal.kcat.reports.print', ['report' => $this->reportService->generateStudentReport($attempt)]);
    }

    public function studentReport(Student $student): View
    {
        return view('kcat::principal.kcat.reports.index', ['attempts' => KcatAttempt::query()->where('student_id', $student->id)->with(['student.classRoom', 'test'])->latest('submitted_at')->paginate(20)]);
    }
}
