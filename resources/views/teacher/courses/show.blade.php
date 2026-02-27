@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-6xl rounded-4 bg-white p-4 p-md-5 shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h2 mb-1 fw-bold">{{ $course->title }}</h1>
                <p class="mb-0 text-muted">Passing score: {{ $course->passing_score }}%</p>
            </div>
            <a href="{{ route('teacher.courses.index') }}" class="btn btn-outline-secondary">Back to Courses</a>
        </div>

        <p class="mt-3 mb-0 text-muted">{{ $course->description }}</p>

        <div class="mt-4">
            <div class="d-flex justify-content-between small mb-1">
                <span>Progress</span>
                <span>{{ $progressPercent }}%</span>
            </div>
            <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progressPercent }}">
                <div class="progress-bar" style="width: {{ $progressPercent }}%"></div>
            </div>
        </div>

        @if($latestAttempt)
            <div class="alert alert-info mt-4 mb-0">
                Latest quiz score: <strong>{{ $latestAttempt->score }}%</strong>
                ({{ $latestAttempt->passed ? 'Passed' : 'Not passed' }})
            </div>
        @endif

        <div class="row g-3 mt-2">
            @forelse($course->lessons as $lesson)
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h2 class="h5 mb-0 fw-semibold">Module {{ $lesson->order }}: {{ $lesson->title }}</h2>
                            </div>
                            <div class="small text-muted">{!! $lesson->content !!}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border mb-0">No lessons available in this course yet.</div>
                </div>
            @endforelse
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            @if($quiz)
                <a href="{{ route('teacher.courses.quiz.take', $course) }}" class="btn btn-primary">Take Quiz</a>
            @endif

            @if($certificate && $certificate->pdf_path)
                <a href="{{ asset('storage/'.$certificate->pdf_path) }}" target="_blank" rel="noopener" class="btn btn-success">
                    Download Certificate
                </a>
            @endif
        </div>
    </section>
@endsection
