@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-6xl rounded-4 bg-white p-4 p-md-5 shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h2 mb-1 fw-bold">My Lessons</h1>
                <p class="mb-0 text-muted">Manage your lesson drafts and submissions.</p>
            </div>
            <a href="{{ route('volunteer.lessons.create') }}" class="btn btn-primary">Create New Lesson</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-4 mb-0">{{ session('success') }}</div>
        @endif

        @if($errors->has('lesson'))
            <div class="alert alert-danger mt-4 mb-0">{{ $errors->first('lesson') }}</div>
        @endif

        <div class="table-responsive mt-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Status</th>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Grades</th>
                        <th>Last Updated</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lessons as $lesson)
                        <tr>
                            <td><span class="badge text-bg-secondary text-uppercase">{{ $lesson->status }}</span></td>
                            <td>{{ $lesson->title }}</td>
                            <td>{{ $lesson->subject }}</td>
                            <td>{{ $lesson->grade_min }} - {{ $lesson->grade_max }}</td>
                            <td>{{ $lesson->updated_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    @if(in_array($lesson->status, ['draft', 'rejected'], true))
                                        <a href="{{ route('volunteer.lessons.edit', $lesson) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('volunteer.lessons.submit', $lesson) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Locked</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No lessons yet. Click "Create New Lesson" to start.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

