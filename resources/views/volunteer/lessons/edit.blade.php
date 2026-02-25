@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-4xl rounded-4 bg-white p-4 p-md-5 shadow-sm">
        <h1 class="h2 fw-bold mb-1">Edit Lesson</h1>
        <p class="text-muted mb-4">You can edit only draft or rejected lessons.</p>

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('volunteer.lessons.update', $lesson) }}" enctype="multipart/form-data" class="row g-3">
            @csrf
            @method('PUT')
            @include('volunteer.lessons.partials.form-fields', ['lesson' => $lesson, 'mode' => 'edit'])

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Draft</button>
                <a href="{{ route('volunteer.lessons.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </section>
@endsection

