<?php

namespace Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat;

use Nsms\Kcat\Http\Controllers\Controller;
use Nsms\Kcat\Models\KcatAssignment;
use Nsms\Kcat\Models\KcatAttempt;
use Nsms\Kcat\Models\KcatTest;
use Illuminate\View\View;

class KcatDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('kcat::career-counselor.kcat.dashboard', [
            'activeTests' => KcatTest::query()->where('status', 'active')->count(),
            'assignedTests' => KcatAssignment::query()->where('status', 'assigned')->count(),
            'completedAttempts' => KcatAttempt::query()->whereIn('status', ['submitted', 'reviewed'])->count(),
            'averageScore' => round((float) KcatAttempt::query()->whereIn('status', ['submitted', 'reviewed'])->avg('percentage'), 2),
            'needsSupport' => KcatAttempt::query()->where('band', 'needs_support')->count(),
            'recentAttempts' => KcatAttempt::query()->with(['student.classRoom', 'test'])->latest('submitted_at')->limit(8)->get(),
        ]);
    }
}
