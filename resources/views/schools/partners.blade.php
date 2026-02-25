@extends('layouts.app')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-extrabold text-black">Our Partner Schools</h1>
        <p class="mt-2 text-sm text-slate-600">Schools collaborating with Hour of Light to improve learning access and quality.</p>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($schools as $school)
            <article class="rounded-2xl bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    @if($school->logo_path)
                        <img src="{{ asset('storage/'.$school->logo_path) }}" alt="{{ $school->school_name }} logo" class="h-14 w-14 rounded-lg object-cover border border-black/10">
                    @else
                        <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-[#1d8cf8]/15 text-lg font-extrabold text-[#1d8cf8]">
                            {{ strtoupper(substr($school->school_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h2 class="text-lg font-extrabold text-black">{{ $school->school_name }}</h2>
                        <p class="text-xs text-slate-600">{{ $school->city ?: '-' }}</p>
                    </div>
                </div>
            </article>
        @empty
            <article class="rounded-2xl bg-white p-6 shadow-sm sm:col-span-2 xl:col-span-3">
                <p class="text-sm text-slate-600">No partner schools have been listed yet.</p>
            </article>
        @endforelse
    </section>
@endsection
