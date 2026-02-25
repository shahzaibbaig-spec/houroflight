@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-5xl rounded-4 bg-white p-4 p-md-5 shadow-sm">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <h1 class="h2 fw-bold mb-1">{{ $lesson->title }}</h1>
                <p class="text-muted mb-0">
                    {{ $lesson->subject }} | Grades {{ $lesson->grade_min }}-{{ $lesson->grade_max }} | {{ $lesson->language }}
                </p>
            </div>
            <div class="text-muted small">
                <div>Views: {{ number_format($lesson->views_count) }}</div>
                <div>Published: {{ optional($lesson->published_at)->format('Y-m-d') ?: '-' }}</div>
            </div>
        </div>

        <hr class="my-4">

        @if($lesson->delivery_mode === 'youtube_link' && !empty($youtubeEmbedUrl))
            <div class="ratio ratio-16x9 mb-4">
                <iframe src="{{ $youtubeEmbedUrl }}" title="Lesson video" allowfullscreen></iframe>
            </div>
        @elseif($lesson->delivery_mode === 'video_upload' && $lesson->video_path)
            <video controls class="w-100 rounded-3 mb-4">
                <source src="{{ asset('storage/'.$lesson->video_path) }}" type="video/mp4">
            </video>
        @elseif($lesson->delivery_mode === 'document_upload' && $lesson->document_path)
            <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
                <span>Lesson document is available for download.</span>
                <a href="{{ asset('storage/'.$lesson->document_path) }}" class="btn btn-sm btn-primary" target="_blank" rel="noopener">
                    Download Document
                </a>
            </div>
        @endif

        <div class="mb-4">
            <h5 class="fw-bold">Description</h5>
            <p class="text-muted mb-0">{{ $lesson->description }}</p>
        </div>

        @if($lesson->learning_objectives)
            <div class="mb-4">
                <h5 class="fw-bold">Learning Objectives</h5>
                <p class="text-muted mb-0">{{ $lesson->learning_objectives }}</p>
            </div>
        @endif

        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-light border">Type: {{ $lesson->lesson_type }}</span>
            <span class="badge text-bg-light border">Mode: {{ str_replace('_', ' ', $lesson->delivery_mode) }}</span>
            @if($lesson->duration_minutes)
                <span class="badge text-bg-light border">Duration: {{ $lesson->duration_minutes }} mins</span>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('lessons.library') }}" class="btn btn-outline-secondary">Back to Library</a>
        </div>
    </section>
@endsection

