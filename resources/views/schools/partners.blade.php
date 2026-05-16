@extends('layouts.app')

@section('content')
    <section class="overflow-hidden rounded-3xl bg-[#0f172a] text-white">
        <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-12 lg:items-center lg:p-10">
            <div class="lg:col-span-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">School Partnerships</p>
                <h1 class="mt-3 text-4xl font-extrabold leading-tight sm:text-5xl">Our Partner Schools</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-white/90 sm:text-base">
                    Schools collaborating with Hour of Light to improve learning access and quality through teacher development, school systems support, and practical EdTech implementation.
                </p>
            </div>
            <div class="lg:col-span-4">
                <div class="rounded-2xl border border-white/20 bg-white/10 p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-white/80">Active School Partners</p>
                    <p class="mt-2 text-4xl font-extrabold">{{ number_format($schools->count()) }}</p>
                    <p class="mt-2 text-sm text-white/85">Approved schools currently showcased in this partner directory.</p>
                </div>
            </div>
        </div>
    </section>

    @if($schools->isNotEmpty())
        <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($schools as $school)
                @php
                    $status = trim((string) ($school->status ?? ''));
                    $statusLabel = $status !== '' ? ucfirst($status) : 'Partner School';
                    $needs = is_array($school->needs) ? collect($school->needs)->filter()->values() : collect();
                @endphp

                <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-black/10 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="relative">
                        @if($school->logo_path)
                            <img
                                src="{{ asset('storage/'.$school->logo_path) }}"
                                alt="Logo of {{ $school->school_name }}"
                                class="h-44 w-full object-cover"
                            >
                        @else
                            <div class="flex h-44 w-full items-center justify-center bg-gradient-to-br from-[#eef6ff] to-[#f7f8f4]">
                                <span class="flex h-20 w-20 items-center justify-center rounded-full border border-[#1d8cf8]/25 bg-white text-3xl font-extrabold text-[#1d8cf8]">
                                    {{ strtoupper(substr($school->school_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif

                        <div class="absolute left-3 top-3">
                            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-700">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <h2 class="text-lg font-extrabold text-black">{{ $school->school_name }}</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            <span class="font-semibold text-slate-700">Location:</span>
                            {{ $school->city ?: 'City not listed' }}
                        </p>

                        <div class="mt-4 rounded-xl bg-[#f7f8f4] px-3 py-2">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Support Status</p>
                            <p class="mt-1 text-sm font-semibold text-black">{{ $statusLabel }}</p>
                        </div>

                        @if($needs->isNotEmpty())
                            <div class="mt-3 rounded-xl bg-[#f7f8f4] px-3 py-2">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Requested Support Areas</p>
                                <p class="mt-1 text-sm text-slate-800">{{ \Illuminate\Support\Str::limit($needs->implode(', '), 90) }}</p>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>
    @else
        <section class="mt-6 rounded-2xl border border-dashed border-black/20 bg-white p-8 text-center shadow-sm sm:p-10">
            <h2 class="text-2xl font-extrabold text-black">No Partner Schools Listed Yet</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                We are actively onboarding schools. This showcase will feature approved partner schools as they join the Hour of Light network.
            </p>
            <a
                href="{{ url('/register-school') }}"
                class="mt-5 inline-flex items-center justify-center rounded-xl bg-[#1d8cf8] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#0b79e4] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]"
            >
                Register Your School
            </a>
        </section>
    @endif

    <section class="mt-6 rounded-3xl bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-3xl font-extrabold text-black">How Schools Benefit</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-700">
            Partner schools receive practical support designed for real classroom and administrative needs, helping teams improve quality learning outcomes at scale.
        </p>
        <div class="mt-5 grid gap-4 md:grid-cols-3">
            <article class="rounded-2xl border border-black/10 bg-[#f7f8f4] p-5">
                <h3 class="text-base font-extrabold text-black">Teacher Capacity Building</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">Structured lesson-planning and instructional support to help teachers deliver stronger classroom experiences.</p>
            </article>
            <article class="rounded-2xl border border-black/10 bg-[#f7f8f4] p-5">
                <h3 class="text-base font-extrabold text-black">Smarter School Systems</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">Guidance on practical school management and timetabling systems to improve efficiency and reduce manual burden.</p>
            </article>
            <article class="rounded-2xl border border-black/10 bg-[#f7f8f4] p-5">
                <h3 class="text-base font-extrabold text-black">EdTech Readiness</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">Classroom technology planning and implementation support to create stronger learning environments.</p>
            </article>
        </div>
    </section>

    <section class="mt-6 rounded-3xl bg-[#1d4ed8] p-6 text-white shadow-sm sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-white/80">Join the Network</p>
                <h2 class="mt-2 text-3xl font-extrabold leading-tight sm:text-4xl">Ready to Partner with Hour of Light?</h2>
                <p class="mt-2 text-sm text-white/90">Register your school and tell us where support is most needed.</p>
            </div>
            <a
                href="{{ url('/register-school') }}"
                class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-[#1d4ed8] transition hover:bg-[#e8f1ff] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
            >
                Register Your School
            </a>
        </div>
    </section>
@endsection
