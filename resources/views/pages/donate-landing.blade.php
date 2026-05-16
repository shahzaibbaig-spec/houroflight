@extends('layouts.app')

@section('content')
    @php
        $secureFundsUrl = route('donate.secure');
        $secureDevicesUrl = route('donate.secure', ['type' => 'hardware']);
        $publicRegisterUrl = route('register');
        $contactEmail = 'info@houroflight.com';
        $isSignedIn = auth()->check();
    @endphp

    <section class="overflow-hidden rounded-3xl bg-[#0f172a] text-white">
        <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-12 lg:items-center lg:p-10">
            <div class="lg:col-span-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Hour of Light</p>
                <h1 class="mt-3 text-4xl font-extrabold leading-tight sm:text-5xl">Support Better Learning Pathways</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-white/90 sm:text-base">
                    Every contribution helps us strengthen teaching quality, improve school systems, and expand practical EdTech support for underserved schools.
                </p>
            </div>
            <div class="lg:col-span-4">
                <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                    <p class="text-sm font-bold uppercase tracking-wide text-white/85">Trust & Transparency</p>
                    <ul class="mt-3 space-y-2 text-sm text-white/90">
                        <li>Verified donation workflows for accountable impact</li>
                        <li>School-focused programs aligned with operational needs</li>
                        <li>Volunteer pathways designed for practical classroom outcomes</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 rounded-3xl bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#1d8cf8]">Donation Options</p>
                <h2 class="mt-2 text-3xl font-extrabold text-black sm:text-4xl">Choose How You Want to Help</h2>
            </div>
            <p class="max-w-xl text-sm leading-6 text-slate-700">Select one path below to view next steps. If final submission requires sign in, you will see that clearly before continuing.</p>
        </div>

        <div class="mt-7 grid gap-4 md:grid-cols-3">
            <article class="rounded-2xl border border-black/10 bg-[#f7f8f4] p-5">
                <h3 class="text-lg font-extrabold text-black">Donate Funds</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">Support teacher training, systems implementation, and classroom upgrades through financial contributions.</p>
                <a href="{{ route('donate.form', ['option' => 'funds']) }}" class="mt-4 inline-flex items-center justify-center rounded-xl bg-[#1d8cf8] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0b79e4] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]">
                    Choose Donate Funds
                </a>
            </article>
            <article class="rounded-2xl border border-black/10 bg-[#f7f8f4] p-5">
                <h3 class="text-lg font-extrabold text-black">Donate Devices</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">Contribute laptops, PCs, and devices that can be refurbished and repurposed for deserving schools.</p>
                <a href="{{ route('donate.form', ['option' => 'devices']) }}" class="mt-4 inline-flex items-center justify-center rounded-xl bg-[#1d8cf8] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0b79e4] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]">
                    Choose Donate Devices
                </a>
            </article>
            <article class="rounded-2xl border border-black/10 bg-[#f7f8f4] p-5">
                <h3 class="text-lg font-extrabold text-black">Donate One Hour</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">Volunteer your teaching time to share model lessons and expand quality learning access for students.</p>
                <a href="{{ route('donate.form', ['option' => 'hour']) }}" class="mt-4 inline-flex items-center justify-center rounded-xl border border-black/20 bg-white px-4 py-2 text-sm font-semibold text-black transition hover:bg-black hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                    Choose Donate One Hour
                </a>
            </article>
        </div>
    </section>

    <section class="mt-6 rounded-3xl bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-extrabold text-black sm:text-3xl">Impact Examples</h2>
        <p class="mt-2 text-sm text-slate-700">Examples based on current planning assumptions in the system. Final impact depends on verified contributions and implementation context.</p>
        <div class="mt-5 grid gap-3 sm:grid-cols-3">
            <article class="rounded-2xl border border-black/10 bg-[#eef6ff] p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-600">Devices</p>
                <p class="mt-2 text-xl font-extrabold text-black">~ PKR {{ number_format((int) config('donations.chromebook_cost_pkr', 20000)) }}</p>
                <p class="mt-1 text-xs text-slate-700">Reference value per Chromebook-equivalent device</p>
            </article>
            <article class="rounded-2xl border border-black/10 bg-[#eef6ff] p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-600">Teacher Training</p>
                <p class="mt-2 text-xl font-extrabold text-black">~ ${{ number_format((int) config('donations.training_hour_cost_usd', 10)) }}</p>
                <p class="mt-1 text-xs text-slate-700">Reference value per teacher training hour</p>
            </article>
            <article class="rounded-2xl border border-black/10 bg-[#f0fdf4] p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-600">Classroom Upgrade</p>
                <p class="mt-2 text-xl font-extrabold text-black">~ ${{ number_format((int) config('donations.classroom_kit_cost_usd', 500)) }}</p>
                <p class="mt-1 text-xs text-slate-700">Reference contribution benchmark per classroom kit</p>
            </article>
        </div>
    </section>

    @if($selectedOption)
        <section class="mt-6 rounded-3xl border border-[#1d8cf8]/25 bg-[#eef6ff] p-6 shadow-sm sm:p-8">
            @if($selectedOption === 'funds')
                <h2 class="text-2xl font-extrabold text-black">Next Step: Donate Funds</h2>
                <p class="mt-2 text-sm leading-6 text-slate-800">You will continue to the secure donation submission area to provide your details and complete the process.</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ $secureFundsUrl }}" class="inline-flex items-center justify-center rounded-xl bg-[#1d8cf8] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#0b79e4] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]">
                        Continue to Secure Donation Area
                    </a>
                    <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center justify-center rounded-xl border border-[#1d8cf8]/30 bg-white px-5 py-3 text-sm font-semibold text-[#1d8cf8] transition hover:bg-[#f2f8ff] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]">
                        Contact Us
                    </a>
                </div>
                @if(!$isSignedIn)
                    <p class="mt-3 text-xs font-medium text-slate-700">Sign in is required before final submission. You will be asked to log in securely when you continue.</p>
                @endif
            @elseif($selectedOption === 'devices')
                <h2 class="text-2xl font-extrabold text-black">Next Step: Donate Devices</h2>
                <p class="mt-2 text-sm leading-6 text-slate-800">You will continue to the secure submission area to share your device details and pickup information.</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ $secureDevicesUrl }}" class="inline-flex items-center justify-center rounded-xl bg-[#1d8cf8] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#0b79e4] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]">
                        Continue to Secure Device Form
                    </a>
                    <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center justify-center rounded-xl border border-[#1d8cf8]/30 bg-white px-5 py-3 text-sm font-semibold text-[#1d8cf8] transition hover:bg-[#f2f8ff] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]">
                        Contact Us
                    </a>
                </div>
                @if(!$isSignedIn)
                    <p class="mt-3 text-xs font-medium text-slate-700">Sign in is required before final submission. You will be asked to log in securely when you continue.</p>
                @endif
            @else
                <h2 class="text-2xl font-extrabold text-black">Next Step: Donate One Hour</h2>
                <p class="mt-2 text-sm leading-6 text-slate-800">Register your interest as a volunteer teacher and our team will guide you on lesson format and submission expectations.</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ $publicRegisterUrl }}" class="inline-flex items-center justify-center rounded-xl bg-black px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                        Register Interest
                    </a>
                    <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center justify-center rounded-xl border border-black/20 bg-white px-5 py-3 text-sm font-semibold text-black transition hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                        Contact Us
                    </a>
                </div>
                @if($isSignedIn)
                    <p class="mt-3 text-xs font-medium text-slate-700">If you already have an account and want to volunteer as a teacher, contact us so we can help align your profile with this pathway.</p>
                @endif
            @endif
        </section>
    @endif
@endsection
