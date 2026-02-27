@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-5xl rounded-4 bg-white p-4 p-md-5 shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h3 mb-1 fw-bold">{{ $quiz->title }}</h1>
                <p class="mb-0 text-muted">{{ $course->title }} | Total marks: {{ $quiz->total_marks }}</p>
            </div>
            <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-outline-secondary">Back to Course</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-4 mb-0">{{ session('success') }}</div>
        @endif

        @if($latestAttempt)
            <div class="alert {{ $latestAttempt->passed ? 'alert-success' : 'alert-warning' }} mt-4 mb-0">
                Latest attempt: <strong>{{ $latestAttempt->score }}%</strong>
                ({{ $latestAttempt->passed ? 'Passed' : 'Not passed' }})
            </div>
        @endif

        @if($certificate && $certificate->pdf_path)
            <div class="mt-3">
                <a href="{{ asset('storage/'.$certificate->pdf_path) }}" target="_blank" rel="noopener" class="btn btn-success">
                    Download Certificate
                </a>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mt-4 mb-0">
                Please answer all required fields correctly.
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.courses.quiz.submit', $course) }}" class="mt-4">
            @csrf
            <div class="row g-3">
                @foreach($quiz->questions as $index => $question)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h2 class="h6 fw-semibold mb-3">
                                    Q{{ $index + 1 }}. {{ $question->question }}
                                    <span class="text-muted">({{ $question->marks }} mark)</span>
                                </h2>

                                @foreach($question->options as $option)
                                    <div class="form-check mb-2">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            id="q{{ $question->id }}o{{ $option->id }}"
                                            value="{{ $option->id }}"
                                            @checked((int) old("answers.{$question->id}") === (int) $option->id)
                                            required
                                        >
                                        <label class="form-check-label" for="q{{ $question->id }}o{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Submit Quiz</button>
            </div>
        </form>
    </section>
@endsection
