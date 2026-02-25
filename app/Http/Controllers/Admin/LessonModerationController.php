<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LessonStatusMail;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class LessonModerationController extends Controller
{
    public function index(Request $request): View
    {
        $allowedStatuses = ['submitted', 'approved', 'rejected', 'published'];
        $status = (string) $request->input('status', '');
        $subject = trim((string) $request->input('subject', ''));

        $query = Lesson::query()
            ->with(['user', 'volunteer'])
            ->whereIn('status', $allowedStatuses);

        if ($status !== '' && in_array($status, $allowedStatuses, true)) {
            $query->where('status', $status);
        }

        if ($subject !== '') {
            $query->where('subject', $subject);
        }

        $lessons = $query
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        $subjects = Lesson::query()
            ->whereIn('status', $allowedStatuses)
            ->select('subject')
            ->distinct()
            ->orderBy('subject')
            ->pluck('subject');

        return view('admin.lessons.index', compact('lessons', 'subjects', 'allowedStatuses'));
    }

    public function show(Lesson $lesson): View
    {
        $lesson->load(['user', 'volunteer', 'reviewer']);
        $youtubeEmbedUrl = null;
        if ($lesson->delivery_mode === 'youtube_link' && $lesson->youtube_url) {
            $youtubeEmbedUrl = $this->toYoutubeEmbedUrl($lesson->youtube_url);
        }

        return view('admin.lessons.show', compact('lesson', 'youtubeEmbedUrl'));
    }

    public function approve(Request $request, Lesson $lesson): RedirectResponse
    {
        if ($lesson->status !== 'submitted') {
            return back()->withErrors(['lesson' => 'Only submitted lessons can be approved.']);
        }

        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $lesson->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => Carbon::now(),
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        $this->notifyTeacher(
            $lesson->fresh(['user']),
            'Lesson Approved - Hour of Light',
            'Your lesson has been approved by the Hour of Light moderation team.'
        );

        return redirect()
            ->route('admin.lessons.show', $lesson)
            ->with('success', 'Lesson approved successfully.');
    }

    public function reject(Request $request, Lesson $lesson): RedirectResponse
    {
        if ($lesson->status !== 'submitted') {
            return back()->withErrors(['lesson' => 'Only submitted lessons can be rejected.']);
        }

        $validated = $request->validate([
            'review_notes' => ['required', 'string', 'max:4000'],
        ]);

        $lesson->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => Carbon::now(),
            'review_notes' => $validated['review_notes'],
            'published_at' => null,
        ]);

        $this->notifyTeacher(
            $lesson->fresh(['user']),
            'Lesson Rejected - Hour of Light',
            'Your lesson was reviewed and rejected. Please review the notes and resubmit.'
        );

        return redirect()
            ->route('admin.lessons.show', $lesson)
            ->with('success', 'Lesson rejected.');
    }

    public function publish(Request $request, Lesson $lesson): RedirectResponse
    {
        if ($lesson->status !== 'approved') {
            return back()->withErrors(['lesson' => 'Only approved lessons can be published.']);
        }

        $lesson->update([
            'status' => 'published',
            'published_at' => Carbon::now(),
        ]);

        $this->notifyTeacher(
            $lesson->fresh(['user']),
            'Lesson Published - Hour of Light',
            'Great news. Your lesson has been published in the public lesson library.'
        );

        return redirect()
            ->route('admin.lessons.show', $lesson)
            ->with('success', 'Lesson published.');
    }

    public function unpublish(Request $request, Lesson $lesson): RedirectResponse
    {
        if ($lesson->status !== 'published') {
            return back()->withErrors(['lesson' => 'Only published lessons can be unpublished.']);
        }

        $lesson->update([
            'status' => 'approved',
            'published_at' => null,
        ]);

        $this->notifyTeacher(
            $lesson->fresh(['user']),
            'Lesson Unpublished - Hour of Light',
            'Your lesson has been unpublished and moved back to approved status.'
        );

        return redirect()
            ->route('admin.lessons.show', $lesson)
            ->with('success', 'Lesson unpublished and moved back to approved.');
    }

    protected function toYoutubeEmbedUrl(string $url): string
    {
        $videoId = null;
        $parts = parse_url($url);
        if (($parts['host'] ?? '') === 'youtu.be') {
            $videoId = ltrim((string) ($parts['path'] ?? ''), '/');
        }

        if (! $videoId && isset($parts['query'])) {
            parse_str($parts['query'], $query);
            $videoId = $query['v'] ?? null;
        }

        if (! $videoId) {
            return $url;
        }

        return 'https://www.youtube.com/embed/'.$videoId.'?rel=0';
    }

    protected function notifyTeacher(Lesson $lesson, string $subject, string $message): void
    {
        if (! $lesson->user?->email) {
            return;
        }

        $mailable = new LessonStatusMail($lesson, $subject, $message);
        $this->dispatchMail($lesson->user->email, $mailable);
    }

    protected function dispatchMail(array|string $to, LessonStatusMail $mailable): void
    {
        try {
            $mailer = Mail::to($to);
            if (config('queue.default') !== 'sync') {
                $mailer->queue($mailable);
            } else {
                $mailer->send($mailable);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
