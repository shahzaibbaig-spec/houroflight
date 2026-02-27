@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-6xl rounded-4 bg-white p-4 p-md-5 shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h2 mb-1 fw-bold">Teacher Courses</h1>
                <p class="mb-0 text-muted">Build your skills through structured self-learning modules.</p>
            </div>
            <a href="{{ route('teacher.certificates.index') }}" class="btn btn-outline-primary">View Certificates</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-4 mb-0">{{ session('success') }}</div>
        @endif

        <div class="row g-4 mt-1">
            @forelse($courses as $course)
                <div class="col-12 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <h2 class="h5 fw-bold mb-1">{{ $course->title }}</h2>
                                    <p class="text-muted small mb-2">Passing score: {{ $course->passing_score }}%</p>
                                </div>
                                @if($course->user_certificate)
                                    <span class="badge text-bg-success">Completed</span>
                                @elseif($course->latest_attempt)
                                    <span class="badge text-bg-warning">In Progress</span>
                                @else
                                    <span class="badge text-bg-secondary">Not Started</span>
                                @endif
                            </div>

                            <p class="small text-muted mb-3">{{ \Illuminate\Support\Str::limit($course->description, 200) }}</p>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progress</span>
                                    <span>{{ $course->progress_percent }}%</span>
                                </div>
                                <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $course->progress_percent }}">
                                    <div class="progress-bar" style="width: {{ $course->progress_percent }}%"></div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                <small class="text-muted">
                                    {{ $course->lessons_count }} lessons
                                </small>
                                <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-primary">Open Course</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border mb-0">No active courses are available right now.</div>
                </div>
            @endforelse
        </div>
    </section>
@endsection
