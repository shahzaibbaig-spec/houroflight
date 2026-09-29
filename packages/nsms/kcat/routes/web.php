<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix(config('kcat.prefix', 'kcat'))->group(function (): void {
    Route::prefix('counselor')->name('kcat.career-counselor.kcat.')->middleware('kcat.permission:view_kcat_panel')->group(function (): void {

                Route::get('/dashboard', \Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatDashboardController::class)->name('dashboard');
                Route::get('/tests', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'index'])->middleware('kcat.permission:manage_kcat_tests')->name('tests.index');
                Route::get('/tests/create', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'create'])->middleware('kcat.permission:manage_kcat_tests')->name('tests.create');
                Route::post('/tests', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'store'])->middleware('kcat.permission:manage_kcat_tests')->name('tests.store');
                Route::get('/tests/{test}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'show'])->middleware('kcat.permission:manage_kcat_tests')->whereNumber('test')->name('tests.show');
                Route::get('/tests/{test}/edit', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'edit'])->middleware('kcat.permission:manage_kcat_tests')->whereNumber('test')->name('tests.edit');
                Route::put('/tests/{test}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'update'])->middleware('kcat.permission:manage_kcat_tests')->whereNumber('test')->name('tests.update');
                Route::post('/tests/{test}/activate', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'activate'])->middleware('kcat.permission:manage_kcat_tests')->whereNumber('test')->name('tests.activate');
                Route::post('/tests/{test}/archive', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'archive'])->middleware('kcat.permission:manage_kcat_tests')->whereNumber('test')->name('tests.archive');
                Route::post('/tests/{test}/adaptive-settings', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'setAdaptiveMode'])->middleware('kcat.permission:manage_kcat_adaptive_settings')->whereNumber('test')->name('tests.adaptive-settings');
                Route::post('/tests/{test}/sections', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'storeSection'])->middleware('kcat.permission:manage_kcat_tests')->whereNumber('test')->name('sections.store');
                Route::put('/sections/{section}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'updateSection'])->middleware('kcat.permission:manage_kcat_tests')->whereNumber('section')->name('sections.update');
                Route::delete('/sections/{section}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatTestController::class, 'destroySection'])->middleware('kcat.permission:manage_kcat_tests')->whereNumber('section')->name('sections.destroy');
                Route::get('/tests/{test}/questions/create', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatQuestionController::class, 'create'])->middleware('kcat.permission:manage_kcat_questions')->whereNumber('test')->name('questions.create');
                Route::post('/questions', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatQuestionController::class, 'store'])->middleware('kcat.permission:manage_kcat_questions')->name('questions.store');
                Route::get('/questions/{question}/edit', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatQuestionController::class, 'edit'])->middleware('kcat.permission:manage_kcat_questions')->whereNumber('question')->name('questions.edit');
                Route::put('/questions/{question}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatQuestionController::class, 'update'])->middleware('kcat.permission:manage_kcat_questions')->whereNumber('question')->name('questions.update');
                Route::delete('/questions/{question}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatQuestionController::class, 'destroy'])->middleware('kcat.permission:manage_kcat_questions')->whereNumber('question')->name('questions.destroy');
                Route::get('/assignments', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAssignmentController::class, 'index'])->middleware('kcat.permission:assign_kcat_tests')->name('assignments.index');
                Route::get('/assignments/create', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAssignmentController::class, 'create'])->middleware('kcat.permission:assign_kcat_tests')->name('assignments.create');
                Route::get('/assignments/students/search', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAssignmentController::class, 'searchStudents'])->middleware('kcat.permission:assign_kcat_tests')->name('assignments.students.search');
                Route::post('/assignments', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAssignmentController::class, 'store'])->middleware('kcat.permission:assign_kcat_tests')->name('assignments.store');
                Route::get('/assignments/{assignment}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAssignmentController::class, 'show'])->middleware('kcat.permission:assign_kcat_tests')->whereNumber('assignment')->name('assignments.show');
                Route::get('/attempts', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAttemptController::class, 'index'])->middleware('kcat.permission:view_kcat_reports')->name('attempts.index');
                Route::get('/attempts/{attempt}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAttemptController::class, 'show'])->middleware('kcat.permission:view_kcat_reports')->whereNumber('attempt')->name('attempts.show');
                Route::delete('/attempts/{attempt}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAttemptController::class, 'destroy'])->middleware('kcat.permission:delete_kcat_results')->whereNumber('attempt')->name('attempts.destroy');
                Route::get('/tests/{test}/manual-entry', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAttemptController::class, 'manualEntry'])->middleware('kcat.permission:manually_enter_kcat_attempt')->whereNumber('test')->name('attempts.manual-entry');
                Route::post('/tests/{test}/manual-entry', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAttemptController::class, 'storeManualEntry'])->middleware('kcat.permission:manually_enter_kcat_attempt')->whereNumber('test')->name('attempts.manual-entry.store');
                Route::get('/adaptive/attempts/{attempt}/next-question', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAdaptiveAttemptController::class, 'nextQuestion'])->middleware('kcat.permission:manage_kcat_adaptive_settings')->whereNumber('attempt')->name('adaptive.next-question');
                Route::post('/adaptive/attempts/{attempt}/submit-answer', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatAdaptiveAttemptController::class, 'submitAnswer'])->middleware('kcat.permission:manage_kcat_adaptive_settings')->whereNumber('attempt')->name('adaptive.submit-answer');
                Route::get('/reports/{attempt}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatReportController::class, 'show'])->middleware('kcat.permission:view_kcat_reports')->whereNumber('attempt')->name('reports.show');
                Route::get('/reports/{attempt}/print', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatReportController::class, 'print'])->middleware('kcat.permission:print_kcat_reports')->whereNumber('attempt')->name('reports.print');
                Route::get('/reports/{attempt}/recommendations', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatReportController::class, 'recommendations'])->middleware('kcat.permission:view_kcat_stream_recommendations')->whereNumber('attempt')->name('reports.recommendations');
                Route::post('/reports/{attempt}/override-stream', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatReportController::class, 'storeOverride'])->middleware('kcat.permission:override_kcat_stream_recommendation')->whereNumber('attempt')->name('reports.override');
                Route::post('/reports/{attempt}/notes', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatReportController::class, 'notes'])->middleware('kcat.permission:manage_kcat_report_notes')->whereNumber('attempt')->name('reports.notes.store');
                Route::get('/question-quality', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatQuestionQualityController::class, 'index'])->middleware('kcat.permission:view_kcat_question_quality')->name('question-quality.index');
                Route::get('/question-quality/{question}', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatQuestionQualityController::class, 'show'])->middleware('kcat.permission:view_kcat_question_quality')->whereNumber('question')->name('question-quality.show');
                Route::post('/question-quality/{question}/review', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatQuestionQualityController::class, 'storeReview'])->middleware('kcat.permission:review_kcat_questions')->whereNumber('question')->name('question-quality.review');
                Route::post('/question-quality/{question}/approve', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatQuestionQualityController::class, 'approve'])->middleware('kcat.permission:review_kcat_questions')->whereNumber('question')->name('question-quality.approve');
                Route::post('/question-quality/{question}/retire', [\Nsms\Kcat\Http\Controllers\CareerCounselor\Kcat\KcatQuestionQualityController::class, 'retire'])->middleware('kcat.permission:retire_kcat_questions')->whereNumber('question')->name('question-quality.retire');
    });
    Route::prefix('principal')->name('kcat.principal.kcat.')->middleware('kcat.permission:view_all_kcat_reports')->group(function (): void {

                Route::get('/reports', [\Nsms\Kcat\Http\Controllers\Principal\Kcat\KcatReportController::class, 'index'])->middleware('kcat.permission:view_all_kcat_reports')->name('reports.index');
                Route::get('/reports/{attempt}', [\Nsms\Kcat\Http\Controllers\Principal\Kcat\KcatReportController::class, 'show'])->middleware('kcat.permission:view_all_kcat_reports')->whereNumber('attempt')->name('reports.show');
                Route::get('/reports/{attempt}/print', [\Nsms\Kcat\Http\Controllers\Principal\Kcat\KcatReportController::class, 'print'])->middleware('kcat.permission:print_kcat_reports')->whereNumber('attempt')->name('reports.print');
                Route::get('/stream-recommendations', [\Nsms\Kcat\Http\Controllers\Principal\Kcat\KcatReportController::class, 'index'])->middleware('kcat.permission:view_kcat_stream_recommendations')->name('stream-recommendations.index');
                Route::get('/students/{student}/reports', [\Nsms\Kcat\Http\Controllers\Principal\Kcat\KcatReportController::class, 'studentReport'])->middleware('kcat.permission:view_all_kcat_reports')->whereNumber('student')->name('students.reports');
                Route::get('/analytics', [\Nsms\Kcat\Http\Controllers\Principal\Kcat\KcatAnalyticsController::class, 'index'])->middleware('kcat.permission:view_all_kcat_reports')->name('analytics.index');
                Route::get('/grade-wise-summary', [\Nsms\Kcat\Http\Controllers\Principal\Kcat\KcatAnalyticsController::class, 'gradeWiseSummary'])->middleware('kcat.permission:view_all_kcat_reports')->name('grade-wise-summary');
                Route::get('/question-quality', [\Nsms\Kcat\Http\Controllers\Principal\Kcat\KcatQuestionQualityController::class, 'index'])->middleware('kcat.permission:view_kcat_question_analytics')->name('question-quality.index');
                Route::get('/question-quality/{question}', [\Nsms\Kcat\Http\Controllers\Principal\Kcat\KcatQuestionQualityController::class, 'show'])->middleware('kcat.permission:view_kcat_question_analytics')->whereNumber('question')->name('question-quality.show');
    });
    Route::prefix('student')->name('kcat.student.kcat.')->middleware('kcat.permission:attempt_kcat_test')->group(function (): void {

            Route::get('/assignments', [\Nsms\Kcat\Http\Controllers\Student\KcatAttemptController::class, 'index'])->name('assignments.index');
            Route::post('/assignments/{assignment}/start', [\Nsms\Kcat\Http\Controllers\Student\KcatAttemptController::class, 'start'])->whereNumber('assignment')->name('assignments.start');
            Route::get('/attempts/{attempt}/question/{index?}', [\Nsms\Kcat\Http\Controllers\Student\KcatAttemptController::class, 'question'])->whereNumber(['attempt', 'index'])->name('attempts.question');
            Route::post('/attempts/{attempt}/questions/{question}', [\Nsms\Kcat\Http\Controllers\Student\KcatAttemptController::class, 'answer'])->whereNumber(['attempt', 'question'])->name('attempts.answer');
            Route::get('/attempts/{attempt}/adaptive/next', [\Nsms\Kcat\Http\Controllers\Student\KcatAttemptController::class, 'adaptiveQuestion'])->whereNumber('attempt')->name('attempts.adaptive.next');
            Route::post('/attempts/{attempt}/adaptive/answer', [\Nsms\Kcat\Http\Controllers\Student\KcatAttemptController::class, 'adaptiveAnswer'])->whereNumber('attempt')->name('attempts.adaptive.answer');
            Route::post('/attempts/{attempt}/submit', [\Nsms\Kcat\Http\Controllers\Student\KcatAttemptController::class, 'submit'])->whereNumber('attempt')->name('attempts.submit');
            Route::get('/attempts/{attempt}/result', [\Nsms\Kcat\Http\Controllers\Student\KcatAttemptController::class, 'result'])->whereNumber('attempt')->name('attempts.result');
    });
});
