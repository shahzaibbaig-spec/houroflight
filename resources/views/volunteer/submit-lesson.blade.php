@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-3xl rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-extrabold text-black">Submit a Lesson</h1>
        <p class="mt-3 text-sm text-slate-700">
            Lesson submission form will be available soon. For now, this is a placeholder page.
        </p>
        <a href="{{ route('volunteer.dashboard') }}" class="hol-btn-primary mt-5">Back to Volunteer Dashboard</a>
    </section>
@endsection

