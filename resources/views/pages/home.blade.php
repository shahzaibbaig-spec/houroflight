@extends('layouts.app')

@section('content')
    @php
        $donateFundsUrl = route('donate.form');
        $donateDevicesUrl = route('donate.form', ['type' => 'hardware']);
        $donateLessonUrl = \Illuminate\Support\Facades\Route::has('volunteer.register')
            ? route('volunteer.register')
            : route('register');
    @endphp

    <section class="overflow-hidden rounded-2xl bg-[#ff4d00] text-white">
        <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-2 lg:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Hour of Light</p>
                <h1 class="mt-3 text-4xl font-extrabold leading-tight sm:text-6xl">Lighting Up Learning<br>for Underserved Schools</h1>
                <p class="mt-4 max-w-xl text-sm text-white/90 sm:text-base">
                    Hour of Light partners with schools to strengthen teaching quality, improve systems, and upgrade classrooms with practical EdTech support so every child can access better learning opportunities.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ $donateFundsUrl }}" class="inline-flex items-center justify-center rounded-xl bg-[#1d8cf8] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0b79e4]">Donate Funds</a>
                    <a href="{{ $donateDevicesUrl }}" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/20">Donate Devices</a>
                    <a href="{{ $donateLessonUrl }}" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/20">Donate One Hour</a>
                </div>
            </div>
            <img src="{{ asset('images/home-hero.jpg') }}" alt="Students and teachers collaborating" class="h-full max-h-[360px] w-full rounded-2xl object-cover">
        </div>
    </section>

    <section class="mt-6 rounded-2xl bg-white p-6 sm:p-8">
        <h2 class="text-3xl font-extrabold text-black">What We Do</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <article class="rounded-xl bg-[#efefe7] p-5">
                <h3 class="text-base font-extrabold text-black">1) Teacher Training (Lesson Planning)</h3>
                <p class="mt-2 text-sm leading-6 text-slate-800">We train teachers to plan stronger lessons, teach with clarity, and assess learning effectively using practical tools that work in real classrooms.</p>
            </article>
            <article class="rounded-xl bg-[#efefe7] p-5">
                <h3 class="text-base font-extrabold text-black">2) School Management Systems (SMS)</h3>
                <p class="mt-2 text-sm leading-6 text-slate-800">We help schools move from manual records to simple digital systems for attendance, exams, fee tracking, and reporting.</p>
            </article>
            <article class="rounded-xl bg-[#efefe7] p-5">
                <h3 class="text-base font-extrabold text-black">3) Timetabling Support</h3>
                <p class="mt-2 text-sm leading-6 text-slate-800">We implement timetabling tools and train school staff to build conflict-free schedules and reduce administrative burden.</p>
            </article>
            <article class="rounded-xl bg-[#efefe7] p-5">
                <h3 class="text-base font-extrabold text-black">4) Adopt a Classroom (EdTech Upgrade)</h3>
                <p class="mt-2 text-sm leading-6 text-slate-800">We adopt classrooms and support schools to set up EdTech-ready learning spaces, including guidance on devices, display setup, and teacher readiness.</p>
            </article>
            <article class="rounded-xl bg-[#efefe7] p-5">
                <h3 class="text-base font-extrabold text-black">5) Donate Devices (Laptops/PCs)</h3>
                <p class="mt-2 text-sm leading-6 text-slate-800">Individuals and organizations can donate used laptops and PCs. We refurbish and repurpose devices for deserving schools.</p>
            </article>
            <article class="rounded-xl border border-[#1d8cf8]/25 bg-[#eef6ff] p-5">
                <h3 class="text-base font-extrabold text-black">6) Donate One Hour (Model Lessons)</h3>
                <p class="mt-2 text-sm leading-6 text-slate-800">Qualified teachers donate one hour of a model lesson, recorded or live. Lessons are shared with students in underprivileged schools to strengthen concept understanding and exposure to quality teaching.</p>
                <a href="{{ $donateLessonUrl }}" class="mt-4 inline-flex items-center justify-center rounded-lg bg-[#1d8cf8] px-4 py-2 text-sm font-bold text-white hover:bg-[#0b79e4]">Donate Your Lesson Now</a>
            </article>
        </div>
        <p class="mt-6 rounded-xl bg-[#ff4d00]/10 p-4 text-sm font-medium text-slate-800">
            Your support helps deliver training, tools, and technology to schools that need it most, turning limited resources into brighter classrooms.
        </p>
    </section>
    @if(!empty($announcement))
        <div id="announcement-popup" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 p-4">
            <div class="relative h-full w-full overflow-hidden rounded-2xl bg-black text-white">
                <button type="button" id="announcement-close" class="absolute right-4 top-4 z-10 rounded-lg bg-white/20 px-4 py-2 text-xs font-bold uppercase text-white">
                    Close
                </button>

                @if($announcement->media_type === 'image' && $announcement->media_path)
                    <img src="{{ asset($announcement->media_path) }}" alt="{{ $announcement->title ?: 'Announcement' }}" class="h-full w-full object-cover">
                @elseif($announcement->media_type === 'video' && $announcement->media_path)
                    <video class="h-full w-full object-cover" controls @if($announcement->autoplay) autoplay muted @endif>
                        <source src="{{ asset($announcement->media_path) }}">
                    </video>
                @elseif($announcement->media_type === 'youtube' && !empty($announcement->youtube_embed_url))
                    <iframe class="h-full w-full" src="{{ $announcement->youtube_embed_url }}" title="Announcement Video" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                @else
                    <div class="flex h-full w-full items-center justify-center p-4 sm:p-8">
                        <div class="announcement-html h-full max-h-full w-full overflow-auto rounded-2xl bg-white/10 p-4 text-left backdrop-blur-sm sm:p-6">
                            @if($announcement->title)
                                <h2 class="text-3xl font-extrabold">{{ $announcement->title }}</h2>
                            @endif
                            @if($announcement->message_html)
                                <div class="mt-4">{!! $announcement->message_html !!}</div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($announcement->message_html && $announcement->media_type !== 'none')
                    <div class="absolute bottom-0 left-0 right-0 bg-black/65 p-5">
                        @if($announcement->title)
                            <h2 class="text-2xl font-extrabold">{{ $announcement->title }}</h2>
                        @endif
                        <div class="announcement-html mt-2 text-sm leading-6">{!! $announcement->message_html !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <script>
            (() => {
                const popup = document.getElementById('announcement-popup');
                const closeBtn = document.getElementById('announcement-close');
                if (!popup || !closeBtn) return;
                setTimeout(() => popup.classList.remove('hidden'), 400);
                popup.classList.add('flex');
                closeBtn.addEventListener('click', () => {
                    popup.classList.add('hidden');
                    popup.classList.remove('flex');
                });
                popup.addEventListener('click', (event) => {
                    if (event.target === popup) {
                        popup.classList.add('hidden');
                        popup.classList.remove('flex');
                    }
                });
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        popup.classList.add('hidden');
                        popup.classList.remove('flex');
                    }
                });
            })();
        </script>
    @endif
@endsection



