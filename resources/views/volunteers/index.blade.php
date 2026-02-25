@extends('layouts.app')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-extrabold text-black">Our Volunteers</h1>
        <p class="mt-2 text-sm text-slate-600">Meet volunteer teachers supporting students through quality lessons.</p>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($volunteers as $volunteer)
            <article class="rounded-2xl bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    @if($volunteer->show_photo_on_website && $volunteer->profile_photo_path)
                        <img src="{{ asset('storage/'.$volunteer->profile_photo_path) }}" alt="{{ $volunteer->user->name }}" class="h-16 w-16 rounded-full object-cover">
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#1d8cf8]/15 text-xl font-extrabold text-[#1d8cf8]">
                            {{ strtoupper(substr($volunteer->user->name ?? 'V', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <a href="{{ route('volunteers.show', $volunteer) }}" class="text-lg font-extrabold text-black hover:text-[#1d8cf8]">
                            {{ $volunteer->user->name }}
                        </a>
                        <p class="text-xs text-slate-600">{{ $volunteer->expertise_subjects ?: 'Volunteer Teacher' }}</p>
                    </div>
                </div>

                <div class="mt-4 space-y-1 text-xs text-slate-700">
                    <p><span class="font-semibold">Years Experience:</span> {{ $volunteer->years_experience ?? '-' }}</p>
                    <p><span class="font-semibold">Qualifications:</span> {{ \Illuminate\Support\Str::limit($volunteer->degree_details ?: '-', 90) }}</p>
                    <p><span class="font-semibold">Awards:</span> {{ \Illuminate\Support\Str::limit($volunteer->awards ?: '-', 90) }}</p>
                    <p><span class="font-semibold">Message:</span> {{ \Illuminate\Support\Str::limit($volunteer->short_bio ?: ($volunteer->teaching_profile_notes ?: '-'), 90) }}</p>
                    <p><span class="font-semibold">Certificates Files:</span> {{ $volunteer->documents->count() }}</p>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    @if($volunteer->degree_document_path)
                        <a href="{{ asset('storage/'.$volunteer->degree_document_path) }}" target="_blank" class="inline-flex items-center rounded-lg border border-[#1d8cf8]/40 px-2 py-1 text-[11px] font-bold text-[#1d8cf8]">
                            Degree
                        </a>
                    @endif
                    @if($volunteer->certificates_document_path)
                        <a href="{{ asset('storage/'.$volunteer->certificates_document_path) }}" target="_blank" class="inline-flex items-center rounded-lg border border-[#1d8cf8]/40 px-2 py-1 text-[11px] font-bold text-[#1d8cf8]">
                            Certificates
                        </a>
                    @endif
                    <a href="{{ route('volunteers.show', $volunteer) }}" class="inline-flex items-center rounded-lg bg-[#1d8cf8] px-2 py-1 text-[11px] font-bold text-white">
                        View Full Profile
                    </a>
                </div>
            </article>
        @empty
            <article class="rounded-2xl bg-white p-6 shadow-sm sm:col-span-2 lg:col-span-3">
                <p class="text-sm text-slate-600">No volunteer profiles are currently available.</p>
            </article>
        @endforelse
    </section>
@endsection
