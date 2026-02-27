<?php

use App\Http\Controllers\AiLessonPlannerController;
use App\Http\Controllers\Admin\LessonModerationController;
use App\Http\Controllers\Admin\DonationVerificationController;
use App\Http\Controllers\Admin\VolunteerModerationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SchoolRegistrationController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Teacher\CertificateController;
use App\Http\Controllers\Teacher\CourseController;
use App\Http\Controllers\Teacher\QuizController;
use App\Http\Controllers\VolunteerDashboardController;
use App\Http\Controllers\VolunteerProfileController;
use App\Http\Controllers\VolunteerPublicController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about-us', 'about');
Route::get('/lessons', [LessonController::class, 'library'])->name('lessons.library');
Route::get('/lessons/{lesson}', [LessonController::class, 'showPublic'])->name('lessons.show');
Route::get('/our-volunteers', [VolunteerPublicController::class, 'index'])->name('volunteers.index');
Route::get('/our-volunteers/{volunteer}', [VolunteerPublicController::class, 'show'])->name('volunteers.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.perform');
    Route::get('/signup', [RegisterController::class, 'create'])->name('signup');
    Route::post('/signup', [RegisterController::class, 'store'])->name('signup.perform');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/register-school', function () {
    return view('schools.register');
});
Route::post('/register-school', [SchoolRegistrationController::class, 'store']);
Route::get('/our-partner-schools', [SchoolRegistrationController::class, 'partners'])->name('schools.partners');

Route::middleware('auth')->group(function () {
    Route::get('/donate', [DonationController::class, 'showForm'])->name('donate.form');
    Route::post('/donate/checkout', [DonationController::class, 'createCheckout'])->name('donate.checkout');
    Route::post('/donate/pledge', [DonationController::class, 'submitPledge'])->name('donate.pledge');
    Route::post('/donate/hardware', [DonationController::class, 'storeHardware'])->name('donate.hardware');
    Route::get('/donate/success', [DonationController::class, 'success'])->name('donate.success');
    Route::get('/donate/cancel', [DonationController::class, 'cancel'])->name('donate.cancel');
    Route::get('/donate/impact', [DonationController::class, 'impact'])->name('donate.impact');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::middleware('auth')->group(function () {
    Route::get('/ai-lesson-planner', [AiLessonPlannerController::class, 'show'])->name('ai.lesson-planner');
    Route::post('/ai-lesson-planner/generate', [AiLessonPlannerController::class, 'generate'])->name('ai.lesson-planner.generate');
    Route::post('/ai-lesson-planner/generate-quiz', [AiLessonPlannerController::class, 'generateQuiz'])->name('ai.lesson-planner.generate-quiz');

    Route::get('/dashboard/donations', [DonationController::class, 'dashboard'])->name('donor.donations');
    Route::get('/dashboard/donations/{donation}', [DonationController::class, 'showDonation'])->name('donor.donations.show');
});

Route::middleware(['auth', 'volunteer.teacher'])->group(function () {
    Route::get('/dashboard/volunteer', [VolunteerDashboardController::class, 'index'])->name('volunteer.dashboard');
    Route::get('/dashboard/volunteer/submit-lesson', [VolunteerDashboardController::class, 'submitLesson'])->name('volunteer.submit-lesson');
    Route::get('/volunteer/profile', [VolunteerProfileController::class, 'edit'])->name('volunteer.profile.edit');
    Route::put('/volunteer/profile', [VolunteerProfileController::class, 'update'])->name('volunteer.profile.update');
    Route::get('/volunteer/lessons', [LessonController::class, 'index'])->name('volunteer.lessons.index');
    Route::get('/volunteer/lessons/create', [LessonController::class, 'create'])->name('volunteer.lessons.create');
    Route::post('/volunteer/lessons', [LessonController::class, 'store'])->name('volunteer.lessons.store');
    Route::get('/volunteer/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('volunteer.lessons.edit');
    Route::put('/volunteer/lessons/{lesson}', [LessonController::class, 'update'])->name('volunteer.lessons.update');
    Route::post('/volunteer/lessons/{lesson}/submit', [LessonController::class, 'submit'])->name('volunteer.lessons.submit');

    Route::get('/teacher/courses', [CourseController::class, 'index'])->name('teacher.courses.index');
    Route::get('/teacher/courses/{course}', [CourseController::class, 'show'])->name('teacher.courses.show');
    Route::get('/teacher/courses/{course}/quiz', [QuizController::class, 'take'])->name('teacher.courses.quiz.take');
    Route::post('/teacher/courses/{course}/quiz', [QuizController::class, 'submit'])->name('teacher.courses.quiz.submit');
    Route::get('/teacher/certificates', [CertificateController::class, 'index'])->name('teacher.certificates.index');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::post('/announcements/{announcement}/toggle', [AdminController::class, 'toggleAnnouncement'])->name('announcements.toggle');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}/password', [AdminController::class, 'updateUserPassword'])->name('users.password.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::post('/donations/{donation}/verify', [DonationVerificationController::class, 'verify'])->name('donations.verify');
    Route::post('/donations/{donation}/approve-proof', [AdminController::class, 'approveDonationProof'])->name('donations.approve-proof');
    Route::delete('/donations/{donation}/cancel-proof', [AdminController::class, 'cancelDonationProof'])->name('donations.cancel-proof');
    Route::delete('/schools/{school}', [AdminController::class, 'removeSchool'])->name('schools.remove');
    Route::get('/volunteers', [VolunteerModerationController::class, 'index'])->name('volunteers.index');
    Route::get('/volunteers/{volunteer}/edit', [VolunteerModerationController::class, 'edit'])->name('volunteers.edit');
    Route::put('/volunteers/{volunteer}', [VolunteerModerationController::class, 'update'])->name('volunteers.update');
    Route::post('/volunteers/{volunteer}/approve', [VolunteerModerationController::class, 'approve'])->name('volunteers.approve');
    Route::post('/volunteers/{volunteer}/reject', [VolunteerModerationController::class, 'reject'])->name('volunteers.reject');
    Route::delete('/volunteers/{volunteer}', [VolunteerModerationController::class, 'destroy'])->name('volunteers.destroy');
    Route::get('/lessons', [LessonModerationController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/{lesson}', [LessonModerationController::class, 'show'])->name('lessons.show');
    Route::post('/lessons/{lesson}/approve', [LessonModerationController::class, 'approve'])->name('lessons.approve');
    Route::post('/lessons/{lesson}/reject', [LessonModerationController::class, 'reject'])->name('lessons.reject');
    Route::post('/lessons/{lesson}/publish', [LessonModerationController::class, 'publish'])->name('lessons.publish');
    Route::post('/lessons/{lesson}/unpublish', [LessonModerationController::class, 'unpublish'])->name('lessons.unpublish');
});

// Fallback route for 404 errors

