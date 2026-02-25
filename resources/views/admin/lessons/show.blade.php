@extends('layouts.app')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-3xl font-extrabold text-black">{{ $lesson->title }}</h1>
                <p class="mt-1 text-sm text-slate-600">
                    {{ $lesson->subject }} | Grades {{ $lesson->grade_min }}-{{ $lesson->grade_max }} | {{ $lesson->language }}
                </p>
            </div>
            <span class="rounded-full bg-[#efefe7] px-3 py-1 text-xs font-bold uppercase text-slate-900">{{ $lesson->status }}</span>
        </div>

        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->has('lesson'))
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first('lesson') }}
            </div>
        @endif

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <article class="rounded-xl border border-black/10 bg-[#efefe7] p-4">
                <h2 class="text-sm font-extrabold uppercase text-slate-700">Teacher Info</h2>
                <p class="mt-2 text-sm text-black"><span class="font-semibold">Name:</span> {{ $lesson->user->name ?? '-' }}</p>
                <p class="mt-1 text-sm text-black"><span class="font-semibold">Email:</span> {{ $lesson->user->email ?? '-' }}</p>
            </article>

            <article class="rounded-xl border border-black/10 bg-[#efefe7] p-4 lg:col-span-2">
                <h2 class="text-sm font-extrabold uppercase text-slate-700">Volunteer Profile</h2>
                <p class="mt-2 text-sm text-black"><span class="font-semibold">Status:</span> {{ $lesson->volunteer->status ?? '-' }}</p>
                <p class="mt-1 text-sm text-black"><span class="font-semibold">Subjects:</span> {{ $lesson->volunteer->expertise_subjects ?? '-' }}</p>
                <p class="mt-1 text-sm text-black"><span class="font-semibold">Grade Levels:</span> {{ $lesson->volunteer->grade_levels ?? '-' }}</p>
                <p class="mt-1 text-sm text-black"><span class="font-semibold">Availability:</span> {{ $lesson->volunteer->availability ?? '-' }}</p>
            </article>
        </div>

        <section class="mt-6 rounded-xl border border-black/10 bg-white p-4">
            <h2 class="text-sm font-extrabold uppercase text-slate-700">Lesson Preview</h2>

            @if($lesson->delivery_mode === 'youtube_link' && !empty($youtubeEmbedUrl))
                <div class="mt-3 overflow-hidden rounded-xl">
                    <iframe src="{{ $youtubeEmbedUrl }}" class="h-[360px] w-full" allowfullscreen title="Lesson video"></iframe>
                </div>
            @elseif($lesson->delivery_mode === 'video_upload' && $lesson->video_path)
                <video controls class="mt-3 h-auto w-full rounded-xl bg-black">
                    <source src="{{ asset('storage/'.$lesson->video_path) }}" type="video/mp4">
                </video>
            @elseif($lesson->delivery_mode === 'document_upload' && $lesson->document_path)
                <div class="mt-3">
                    <a href="{{ asset('storage/'.$lesson->document_path) }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-lg bg-[#1d8cf8] px-4 py-2 text-sm font-bold text-white">
                        Download Document
                    </a>
                </div>
            @else
                <p class="mt-3 text-sm text-slate-600">No preview source available.</p>
            @endif

            <div class="mt-4 text-sm text-slate-800">
                <p><span class="font-semibold">Description:</span> {{ $lesson->description }}</p>
                @if($lesson->learning_objectives)
                    <p class="mt-2"><span class="font-semibold">Learning Objectives:</span> {{ $lesson->learning_objectives }}</p>
                @endif
                <p class="mt-2"><span class="font-semibold">Delivery Mode:</span> {{ str_replace('_', ' ', $lesson->delivery_mode) }}</p>
                <p class="mt-1"><span class="font-semibold">Lesson Type:</span> {{ $lesson->lesson_type }}</p>
                <p class="mt-1"><span class="font-semibold">Duration:</span> {{ $lesson->duration_minutes ?? '-' }} mins</p>
                <p class="mt-1"><span class="font-semibold">Views:</span> {{ number_format($lesson->views_count) }}</p>
                <p class="mt-1"><span class="font-semibold">Reviewed By:</span> {{ $lesson->reviewer->name ?? '-' }}</p>
                <p class="mt-1"><span class="font-semibold">Reviewed At:</span> {{ optional($lesson->reviewed_at)->format('Y-m-d H:i') ?? '-' }}</p>
                <p class="mt-1"><span class="font-semibold">Published At:</span> {{ optional($lesson->published_at)->format('Y-m-d H:i') ?? '-' }}</p>
            </div>
        </section>

        <section class="mt-6 rounded-xl border border-black/10 bg-white p-4">
            <h2 class="text-sm font-extrabold uppercase text-slate-700">Moderation Actions</h2>

            <form method="POST" class="mt-3 space-y-3">
                @csrf
                <label class="hol-label" for="review_notes">Review Notes</label>
                <textarea id="review_notes" name="review_notes" rows="4" class="hol-input" placeholder="Optional for approval, required for rejection">{{ old('review_notes', $lesson->review_notes) }}</textarea>

                <div class="flex flex-wrap gap-2">
                    @if($lesson->status === 'submitted')
                        <button type="submit" formaction="{{ route('admin.lessons.approve', $lesson) }}" class="hol-btn-secondary">Approve</button>
                        <button type="submit" formaction="{{ route('admin.lessons.reject', $lesson) }}" class="inline-flex items-center rounded-xl bg-[#ff4d00] px-4 py-3 text-sm font-semibold text-white">Reject</button>
                    @endif
                </div>
            </form>

            <div class="mt-3 flex flex-wrap gap-2">
                @if($lesson->status === 'approved')
                    <form method="POST" action="{{ route('admin.lessons.publish', $lesson) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white">Publish</button>
                    </form>
                @endif

                @if($lesson->status === 'published')
                    <form method="POST" action="{{ route('admin.lessons.unpublish', $lesson) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-xl border border-black/20 bg-white px-4 py-3 text-sm font-semibold text-slate-900">Unpublish</button>
                    </form>
                @endif
            </div>
        </section>

        <div class="mt-6">
            <a href="{{ route('admin.lessons.index') }}" class="inline-flex items-center rounded-lg border border-black/15 px-4 py-2 text-sm font-semibold text-slate-900">Back to Lessons</a>
        </div>
    </section>
@endsection
