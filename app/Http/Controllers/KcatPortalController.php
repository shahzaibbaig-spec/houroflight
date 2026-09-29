<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\KcatPersonalReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Nsms\Kcat\Models\KcatAssignment;
use Nsms\Kcat\Models\KcatAttempt;
use Nsms\Kcat\Models\KcatTest;
use Nsms\Kcat\Services\Kcat\KcatAttemptService;
use Nsms\Kcat\Services\StudentResolver;
use Symfony\Component\HttpFoundation\Response;

class KcatPortalController extends Controller
{
    public function __invoke(Request $request): View
    {
        $tests = KcatTest::whereNotNull('self_service_key')->where('status', 'active')
            ->whereHas('questions', fn ($q) => $q->where('is_active', true)->whereNull('retired_at'))
            ->withCount(['questions' => fn ($q) => $q->where('is_active', true)->whereNull('retired_at')])->get();
        $attempts = $request->user()
            ? KcatAttempt::whereHas('student', fn ($q) => $q->where('user_id', $request->user()->id))
                ->with('test')->latest('id')->get()
            : collect();

        return view('kcat.index', compact('tests', 'attempts'));
    }

    public function start(Request $request, KcatTest $test, StudentResolver $students, KcatAttemptService $attempts): RedirectResponse
    {
        abort_unless(config('kcat.self_service') && $test->self_service_key && $test->status === 'active', 404);
        abort_unless($test->questions()->where('is_active', true)->whereNull('retired_at')->exists(), 404);
        $attempt = DB::transaction(function () use ($request, $test, $students, $attempts) {
            // Serialize repeated starts for the same account, including double clicks.
            $user = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $student = $students->resolveStudentForUser($user);
            $assignment = KcatAssignment::firstOrCreate([
                'kcat_test_id' => $test->id, 'student_id' => $student->id, 'assigned_to_type' => 'student',
            ], ['session' => now()->year.'-'.(now()->year + 1), 'assigned_at' => now(), 'status' => 'assigned']);

            return $attempts->startAttempt($assignment, $student);
        });
        if (in_array($attempt->status, ['submitted', 'reviewed'], true)) {
            return redirect()->route('kcat.student.kcat.attempts.result', $attempt);
        }
        return $attempt->is_adaptive
            ? redirect()->route('kcat.student.kcat.attempts.adaptive.next', $attempt)
            : redirect()->route('kcat.student.kcat.attempts.question', [$attempt, 0]);
    }

    public function download(Request $request, KcatAttempt $attempt, KcatPersonalReport $reports): Response
    {
        abort_unless((int) $attempt->student?->user_id === (int) $request->user()->id, 403);
        abort_unless(in_array($attempt->status, ['submitted', 'reviewed'], true), 403, 'Complete your test to download its report.');

        return Pdf::loadView('pdf.kcat_report', ['report' => $reports->build($attempt)])
            ->setPaper('a4')->setOptions(['isRemoteEnabled' => false, 'isPhpEnabled' => false])
            ->download('KCAT-Report-'.$attempt->id.'.pdf')
            ->header('Cache-Control', 'private, no-store');
    }
}
