@extends('layouts.app')

@section('content')
    <section class="overflow-hidden rounded-2xl bg-[#ff4d00] text-white">
        <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/80">About Hour of Light</p>
                <h1 class="mt-3 text-4xl font-extrabold leading-none sm:text-6xl">Bridging the<br>Digital Divide</h1>
                <p class="mt-4 text-base font-medium text-white/90">Empowering Schools Through Technology & Innovation.</p>
            </div>
            <img src="{{ asset('images/founder.jpg') }}" alt="Parizay Fatima Baig speaking at school" class="h-[420px] w-full rounded-2xl bg-[#efefe7] p-2 object-contain sm:h-[520px]" onerror="this.style.display='none'">
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-3">
        <article class="rounded-2xl bg-white p-6 lg:col-span-2">
            <h2 class="text-3xl font-extrabold text-black">About Hour of Light: Illuminating Education Through Technology</h2>
            <p class="mt-4 text-sm leading-7 text-slate-800">
                Hello! My name is Parizay Fatima Baig. I am a Grade 6 student at Beaconhouse School System and founder of Hour of Light.
            </p>
            <p class="mt-3 text-sm leading-7 text-slate-800">
                I do think that talent is distributed evenly, but opportunity is not. In this increasingly digitally driven world, too many kids in under-resourced communities are being left behind simply because they do not have the right tools. That realization is how Hour of Light went from a simple idea into a movement for digital inclusion.
            </p>
        </article>
        <article class="rounded-2xl bg-[#1d8cf8] p-6 text-white">
            <h3 class="text-2xl font-extrabold">Our Vision</h3>
            <p class="mt-3 text-sm leading-7 text-white/95">
                Hour of Light is a nonprofit organization committed to reinventing traditional classrooms as state-of-the-art learning centers.
            </p>
            <p class="mt-3 text-sm leading-7 text-white/95">
                We support United Nations Sustainable Development Goal 4 (Quality Education for All) by ensuring under-resourced schools have the hardware, software, and skills required to succeed in the 21st century.
            </p>
        </article>
    </section>

    <section class="mt-6 rounded-2xl bg-white p-6 sm:p-8">
        <h3 class="text-3xl font-extrabold text-black">What We Do</h3>
        <p class="mt-3 text-sm leading-7 text-slate-800">We do not just donate equipment; we build ecosystems for success. Our work centers on three core pillars:</p>
        @php
            $volunteerRegisterUrl = \Illuminate\Support\Facades\Route::has('volunteer.register')
                ? route('volunteer.register')
                : route('signup');
        @endphp
        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl bg-[#efefe7] p-5">
                <p class="text-sm font-extrabold text-black">Chromebooks & Hardware Distribution</p>
                <p class="mt-2 text-sm leading-6 text-slate-800">We donate high-quality Chromebooks and digital devices to small, under-funded schools, giving students immediate access to the internet's vast library of knowledge.</p>
            </div>
            <div class="rounded-xl bg-[#efefe7] p-5">
                <p class="text-sm font-extrabold text-black">Teacher Training & Digital Literacy</p>
                <p class="mt-2 text-sm leading-6 text-slate-800">Technology is only as good as the person using it. We conduct training sessions for teachers so they can integrate technology effectively into their curriculum.</p>
            </div>
            <div class="rounded-xl bg-[#efefe7] p-5">
                <p class="text-sm font-extrabold text-black">Implementation of School Management Systems</p>
                <p class="mt-2 text-sm leading-6 text-slate-800">We install efficient systems to digitize administrative workflows, allowing educators to spend less time on paperwork and more time on what matters: teaching.</p>
            </div>
            <div class="rounded-xl border border-[#1d8cf8]/20 bg-[#eef6ff] p-5">
                <p class="text-sm font-extrabold text-black">Teacher Volunteer Program - Donate One Hour</p>
                <p class="mt-2 text-sm leading-6 text-slate-800">
                    We invite qualified teachers to donate one hour of a model lesson. These lessons will be shared with students in underprivileged schools to improve conceptual understanding and exposure to quality teaching.
                </p>
                <ul class="mt-3 list-disc space-y-1 pl-5 text-sm leading-6 text-slate-800">
                    <li>Recorded lesson library</li>
                    <li>Live virtual guest sessions</li>
                    <li>Subject-based mentoring</li>
                </ul>
                <a href="{{ $volunteerRegisterUrl }}" class="mt-4 inline-flex items-center justify-center rounded-lg bg-[#1d8cf8] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#0b79e4]">
                    Become a Volunteer Teacher
                </a>
            </div>
        </div>
    </section>

    <section class="mt-6 rounded-2xl bg-[#ff4d00] p-6 text-white sm:p-8">
        <h3 class="text-4xl font-extrabold leading-none sm:text-5xl">Help us connect the unconnected.</h3>
        <p class="mt-3 text-2xl font-extrabold">Donate and create a digital revolution.</p>
        <p class="mt-4 max-w-4xl text-sm leading-7 text-white/95">
            We seek to put the world at the fingertips of every child, but we cannot do it alone. When you support the Hour of Light, you are not just buying a laptop; you are unlocking a future. You are providing a student with their first coding lesson, a teacher with all the tools they need to track student progress efficiently, and ultimately, you are closing the gap between poverty and potential.
        </p>
        <div class="mt-5 rounded-xl bg-white p-5 text-black">
            <p class="text-sm font-extrabold">Your donation supports:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm leading-6">
                <li>Procurement of Chromebooks for classrooms devoid of digital infrastructure.</li>
                <li>Licenses for crucial School Management Systems Software.</li>
                <li>On-site workshops for digital pedagogy training of educators.</li>
            </ul>
        </div>
        <p class="mt-4 text-sm leading-7 text-white/95">Join us to make sure Quality Education is for All. Together, let us ensure that no child is left offline. Donate today so our flow of technology may reach the schools that most need it.</p>
        <a href="{{ url('/donate') }}" class="mt-5 inline-flex items-center justify-center rounded-xl bg-[#1d8cf8] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#0b79e4]">Donate Today</a>
    </section>
@endsection
