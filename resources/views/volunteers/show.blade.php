@extends('layouts.app')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start gap-4">
            @if($volunteer->show_photo_on_website && $volunteer->profile_photo_path)
                <img src="{{ asset('storage/'.$volunteer->profile_photo_path) }}" alt="{{ $volunteer->user->name }}" class="h-24 w-24 rounded-full object-cover">
            @else
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-[#1d8cf8]/15 text-3xl font-extrabold text-[#1d8cf8]">
                    {{ strtoupper(substr($volunteer->user->name ?? 'V', 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-3xl font-extrabold text-black">{{ $volunteer->user->name }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $volunteer->expertise_subjects }}</p>
                <p class="text-sm text-slate-600">Years of Experience: {{ $volunteer->years_experience ?? '-' }}</p>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-2">
        <article class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-xl font-extrabold text-black">Teaching Profile</h2>
            <p class="mt-3 text-sm text-slate-800"><span class="font-semibold">Subjects & Expertise:</span> {{ $volunteer->expertise_subjects ?: '-' }}</p>
            <p class="mt-2 text-sm text-slate-800"><span class="font-semibold">Grade Levels:</span> {{ $volunteer->grade_levels ?: '-' }}</p>
            <p class="mt-2 text-sm text-slate-800"><span class="font-semibold">Availability:</span> {{ $volunteer->availability ?: '-' }}</p>
            <p class="mt-2 text-sm text-slate-800"><span class="font-semibold">Lecture Format:</span> {{ ucfirst($volunteer->lesson_format ?: '-') }}</p>
            <p class="mt-2 text-sm text-slate-800"><span class="font-semibold">Short Bio:</span> {{ $volunteer->short_bio ?: '-' }}</p>
            <p class="mt-2 text-sm text-slate-800"><span class="font-semibold">Additional Teaching Notes:</span> {{ $volunteer->teaching_profile_notes ?: '-' }}</p>
        </article>

        <article class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-xl font-extrabold text-black">Qualifications</h2>
            <p class="mt-3 text-sm text-slate-800"><span class="font-semibold">Degree Details:</span> {{ $volunteer->degree_details ?: '-' }}</p>
            <p class="mt-2 text-sm text-slate-800"><span class="font-semibold">Awards:</span> {{ $volunteer->awards ?: '-' }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                @if($volunteer->degree_document_path)
                    <a href="{{ asset('storage/'.$volunteer->degree_document_path) }}" target="_blank" class="inline-flex items-center rounded-lg bg-[#1d8cf8] px-4 py-2 text-xs font-bold text-white">View Degree Document</a>
                @endif
                @if($volunteer->certificates_document_path)
                    <a href="{{ asset('storage/'.$volunteer->certificates_document_path) }}" target="_blank" class="inline-flex items-center rounded-lg border border-[#1d8cf8]/40 px-4 py-2 text-xs font-bold text-[#1d8cf8]">View Certificates/Awards</a>
                @endif
            </div>

            @if($volunteer->documents->isNotEmpty())
                <h3 class="mt-5 text-sm font-extrabold uppercase tracking-wide text-slate-700">Certificates & Awards Gallery</h3>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    @foreach($volunteer->documents as $document)
                        <div class="rounded-xl border border-black/10 p-2">
                            @if(str_starts_with((string) $document->mime_type, 'image/'))
                                <img src="{{ asset('storage/'.$document->file_path) }}" alt="{{ $document->original_name }}" class="h-36 w-full rounded-lg object-cover">
                            @else
                                <div class="flex h-36 items-center justify-center rounded-lg bg-[#efefe7] text-xs font-bold text-slate-700">
                                    {{ strtoupper(pathinfo($document->original_name, PATHINFO_EXTENSION)) ?: 'FILE' }}
                                </div>
                            @endif
                            <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="mt-2 block truncate text-xs font-semibold text-[#1d8cf8]">
                                {{ $document->original_name }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>
    </section>

    <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-extrabold text-black">Published Lectures</h2>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            @forelse($lessons as $lesson)
                <article class="rounded-xl border border-black/10 p-4">
                    <h3 class="text-base font-extrabold text-black">{{ $lesson->title }}</h3>
                    <p class="mt-1 text-xs text-slate-600">{{ $lesson->subject }} | Grades {{ $lesson->grade_min }}-{{ $lesson->grade_max }}</p>
                    <a href="{{ route('lessons.show', $lesson) }}" class="mt-3 inline-flex items-center rounded-lg border border-[#1d8cf8]/40 px-3 py-2 text-xs font-bold text-[#1d8cf8]">Open Lecture</a>
                </article>
            @empty
                <p class="text-sm text-slate-600">No published lectures yet.</p>
            @endforelse
        </div>
    </section>
@endsection
