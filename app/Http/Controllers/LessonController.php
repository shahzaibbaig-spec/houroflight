<?php

namespace App\Http\Controllers;

use App\Mail\LessonStatusMail;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function library(Request $request): View
    {
        $query = Lesson::query()->where('status', 'published');

        $query->when($request->filled('subject'), function ($q) use ($request) {
            $q->where('subject', $request->string('subject')->toString());
        });

        $query->when($request->filled('grade'), function ($q) use ($request) {
            $grade = (int) $request->input('grade');
            $q->where('grade_min', '<=', $grade)->where('grade_max', '>=', $grade);
        });

        $query->when($request->filled('language'), function ($q) use ($request) {
            $q->where('language', $request->string('language')->toString());
        });

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $keyword = trim((string) $request->input('keyword'));
            $q->where(function ($inner) use ($keyword) {
                $inner->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        });

        $lessons = $query
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $subjects = Lesson::query()
            ->where('status', 'published')
            ->whereNotNull('subject')
            ->distinct()
            ->orderBy('subject')
            ->pluck('subject');

        $languages = Lesson::query()
            ->where('status', 'published')
            ->whereNotNull('language')
            ->distinct()
            ->orderBy('language')
            ->pluck('language');

        return view('lessons.library', compact('lessons', 'subjects', 'languages'));
    }

    public function showPublic(Lesson $lesson): View
    {
        if ($lesson->status !== 'published') {
            abort(404);
        }

        Lesson::query()->whereKey($lesson->id)->increment('views_count');
        $lesson->refresh();

        $youtubeEmbedUrl = null;
        if ($lesson->delivery_mode === 'youtube_link' && $lesson->youtube_url) {
            $youtubeEmbedUrl = $this->toYoutubeEmbedUrl($lesson->youtube_url);
        }

        return view('lessons.show', compact('lesson', 'youtubeEmbedUrl'));
    }

    public function index(Request $request): View
    {
        $lessons = Lesson::query()
            ->where('user_id', $request->user()->id)
            ->latest('updated_at')
            ->get();

        return view('volunteer.lessons.index', compact('lessons'));
    }

    public function create(): View
    {
        return view('volunteer.lessons.create');
    }

    public function store(StoreLessonRequest $request): RedirectResponse
    {
        $user = $request->user()->loadMissing('volunteer');
        $validated = $request->validated();

        $videoPath = null;
        $documentPath = null;
        $youtubeUrl = null;

        $videoFile = $this->resolveUpload($request, 'video', 'video_file');
        $documentFile = $this->resolveUpload($request, 'document', 'document_file');

        if ($validated['delivery_mode'] === 'video_upload' && $videoFile) {
            $videoPath = $videoFile->store('lessons/videos', 'public');
        }

        if ($validated['delivery_mode'] === 'document_upload' && $documentFile) {
            $documentPath = $documentFile->store('lessons/docs', 'public');
        }

        if ($validated['delivery_mode'] === 'youtube_link') {
            $youtubeUrl = $validated['youtube_url'];
        }

        Lesson::create([
            'user_id' => $user->id,
            'volunteer_id' => optional($user->volunteer)->id,
            'title' => $validated['title'],
            'subject' => $validated['subject'],
            'grade_min' => $validated['grade_min'],
            'grade_max' => $validated['grade_max'],
            'lesson_type' => $validated['lesson_type'],
            'delivery_mode' => $validated['delivery_mode'],
            'youtube_url' => $youtubeUrl,
            'video_path' => $videoPath,
            'document_path' => $documentPath,
            'description' => $validated['description'],
            'learning_objectives' => $validated['learning_objectives'] ?? null,
            'language' => $validated['language'],
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'status' => 'draft',
        ]);

        return redirect()
            ->route('volunteer.lessons.index')
            ->with('success', 'Lesson saved as draft.');
    }

    public function edit(Request $request, Lesson $lesson): View
    {
        $this->ensureOwnership($request, $lesson);
        $this->ensureEditable($lesson);

        return view('volunteer.lessons.edit', compact('lesson'));
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson): RedirectResponse
    {
        $this->ensureOwnership($request, $lesson);
        $this->ensureEditable($lesson);

        $validated = $request->validated();

        $videoPath = $lesson->video_path;
        $documentPath = $lesson->document_path;
        $youtubeUrl = $lesson->youtube_url;

        $videoFile = $this->resolveUpload($request, 'video', 'video_file');
        $documentFile = $this->resolveUpload($request, 'document', 'document_file');

        if ($validated['delivery_mode'] === 'video_upload') {
            $youtubeUrl = null;
            if ($documentPath) {
                Storage::disk('public')->delete($documentPath);
                $documentPath = null;
            }
            if ($videoFile) {
                if ($videoPath) {
                    Storage::disk('public')->delete($videoPath);
                }
                $videoPath = $videoFile->store('lessons/videos', 'public');
            }
        } elseif ($validated['delivery_mode'] === 'document_upload') {
            $youtubeUrl = null;
            if ($videoPath) {
                Storage::disk('public')->delete($videoPath);
                $videoPath = null;
            }
            if ($documentFile) {
                if ($documentPath) {
                    Storage::disk('public')->delete($documentPath);
                }
                $documentPath = $documentFile->store('lessons/docs', 'public');
            }
        } else {
            $youtubeUrl = $validated['youtube_url'];
            if ($videoPath) {
                Storage::disk('public')->delete($videoPath);
                $videoPath = null;
            }
            if ($documentPath) {
                Storage::disk('public')->delete($documentPath);
                $documentPath = null;
            }
        }

        $lesson->update([
            'title' => $validated['title'],
            'subject' => $validated['subject'],
            'grade_min' => $validated['grade_min'],
            'grade_max' => $validated['grade_max'],
            'lesson_type' => $validated['lesson_type'],
            'delivery_mode' => $validated['delivery_mode'],
            'youtube_url' => $youtubeUrl,
            'video_path' => $videoPath,
            'document_path' => $documentPath,
            'description' => $validated['description'],
            'learning_objectives' => $validated['learning_objectives'] ?? null,
            'language' => $validated['language'],
            'duration_minutes' => $validated['duration_minutes'] ?? null,
        ]);

        return redirect()
            ->route('volunteer.lessons.index')
            ->with('success', 'Lesson updated.');
    }

    public function submit(Request $request, Lesson $lesson): RedirectResponse
    {
        $this->ensureOwnership($request, $lesson);

        if (! in_array($lesson->status, ['draft', 'rejected'], true)) {
            return back()->withErrors(['lesson' => 'Only draft or rejected lessons can be submitted.']);
        }

        $lesson->update([
            'status' => 'submitted',
            'review_notes' => null,
        ]);

        $lesson = $lesson->fresh(['user', 'volunteer']);
        $this->notifyAdminsLessonSubmitted($lesson);

        return redirect()
            ->route('volunteer.lessons.index')
            ->with('success', 'Lesson submitted for review.');
    }

    protected function ensureOwnership(Request $request, Lesson $lesson): void
    {
        if ((int) $lesson->user_id !== (int) $request->user()->id) {
            abort(403, 'You can only manage your own lessons.');
        }
    }

    protected function ensureEditable(Lesson $lesson): void
    {
        if (! in_array($lesson->status, ['draft', 'rejected'], true)) {
            abort(403, 'This lesson can no longer be edited.');
        }
    }

    protected function resolveUpload(Request $request, string $primaryKey, string $fallbackKey): ?UploadedFile
    {
        if ($request->hasFile($primaryKey)) {
            return $request->file($primaryKey);
        }

        if ($request->hasFile($fallbackKey)) {
            return $request->file($fallbackKey);
        }

        return null;
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

    protected function notifyAdminsLessonSubmitted(Lesson $lesson): void
    {
        $adminLink = route('admin.lessons.show', $lesson);
        $subject = 'New Lesson Submitted - Hour of Light';
        $message = 'A teacher has submitted a lesson for moderation. Please review it in the admin panel.';
        $mailable = new LessonStatusMail($lesson, $subject, $message, $adminLink);

        $this->dispatchMail([
            'info@houroflight.com',
            'shahzaib.baig@gmail.com',
        ], $mailable);
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
