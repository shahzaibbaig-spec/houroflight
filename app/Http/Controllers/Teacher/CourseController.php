<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $courses = Course::query()
            ->where('is_active', true)
            ->withCount('lessons')
            ->with([
                'quizzes' => fn ($query) => $query
                    ->withCount('questions')
                    ->with([
                        'attempts' => fn ($attempts) => $attempts
                            ->where('user_id', (int) $user->id)
                            ->latest('attempted_at'),
                    ]),
            ])
            ->orderBy('title')
            ->get();

        $certificates = Certificate::query()
            ->where('user_id', (int) $user->id)
            ->get()
            ->keyBy('course_id');

        $courses = $courses->map(function (Course $course) use ($certificates): Course {
            $quiz = $course->quizzes->first();
            $latestAttempt = $quiz?->attempts->first();
            $certificate = $certificates->get($course->id);

            $progress = 0;
            if ($certificate) {
                $progress = 100;
            } elseif ($latestAttempt) {
                $progress = min((int) $latestAttempt->score, 99);
            }

            $course->setAttribute('progress_percent', $progress);
            $course->setAttribute('latest_attempt', $latestAttempt);
            $course->setAttribute('user_certificate', $certificate);

            return $course;
        });

        return view('teacher.courses.index', [
            'courses' => $courses,
        ]);
    }

    public function show(Course $course): View
    {
        abort_unless($course->is_active, 404);

        $course->load([
            'lessons' => fn ($query) => $query->orderBy('order'),
            'quizzes' => fn ($query) => $query->with([
                'attempts' => fn ($attempts) => $attempts
                    ->where('user_id', (int) auth()->id())
                    ->latest('attempted_at'),
            ]),
        ]);

        $quiz = $course->quizzes->first();
        $latestAttempt = $quiz?->attempts->first();
        $certificate = Certificate::query()
            ->where('user_id', (int) auth()->id())
            ->where('course_id', (int) $course->id)
            ->first();

        $progress = 0;
        if ($certificate) {
            $progress = 100;
        } elseif ($latestAttempt) {
            $progress = min((int) $latestAttempt->score, 99);
        }

        return view('teacher.courses.show', [
            'course' => $course,
            'quiz' => $quiz,
            'latestAttempt' => $latestAttempt,
            'certificate' => $certificate,
            'progressPercent' => $progress,
        ]);
    }
}
