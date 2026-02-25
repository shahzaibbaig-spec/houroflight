@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-6xl rounded-4 bg-white p-4 p-md-5 shadow-sm">
        <h1 class="h2 fw-bold mb-1">Lesson Library</h1>
        <p class="text-muted mb-4">Explore published volunteer lessons for schools and students.</p>

        <form method="GET" action="{{ route('lessons.library') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select name="subject" class="form-select">
                    <option value="">All subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject }}" @selected(request('subject') === $subject)>{{ $subject }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Grade</label>
                <input type="number" name="grade" min="1" max="12" value="{{ request('grade') }}" class="form-control" placeholder="1-12">
            </div>

            <div class="col-md-3">
                <label class="form-label">Language</label>
                <select name="language" class="form-select">
                    <option value="">All languages</option>
                    @foreach($languages as $language)
                        <option value="{{ $language }}" @selected(request('language') === $language)>{{ $language }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Keyword</label>
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Search title or description">
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('lessons.library') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="row g-3">
            @forelse($lessons as $lesson)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <h5 class="card-title mb-1">{{ $lesson->title }}</h5>
                                <span class="badge text-bg-primary">{{ $lesson->subject }}</span>
                            </div>
                            <p class="text-muted small mb-2">
                                Grades {{ $lesson->grade_min }}-{{ $lesson->grade_max }} |
                                {{ $lesson->language }} |
                                {{ ucfirst(str_replace('_', ' ', $lesson->delivery_mode)) }}
                            </p>
                            <p class="card-text text-muted small flex-grow-1">
                                {{ \Illuminate\Support\Str::limit($lesson->description, 120) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    {{ optional($lesson->published_at)->format('Y-m-d') ?: '-' }}
                                </small>
                                <a href="{{ route('lessons.show', $lesson) }}" class="btn btn-sm btn-outline-primary">View Lesson</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border mb-0">No published lessons found for the selected filters.</div>
                </div>
            @endforelse
        </div>

        @if($lessons->hasPages())
            <div class="mt-4">
                {{ $lessons->links() }}
            </div>
        @endif
    </section>
@endsection

