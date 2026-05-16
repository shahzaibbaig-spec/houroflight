@extends('layouts.app')

@section('content')
    @php
        $donateFundsUrl = route('donate.form');
        $donateDevicesUrl = route('donate.form', ['type' => 'hardware']);
        $donateLessonUrl = \Illuminate\Support\Facades\Route::has('volunteer.register')
            ? route('volunteer.register')
            : route('register');

        $stats = [
            'schools' => 0,
            'volunteers' => 0,
            'lessons' => 0,
            'donations' => 0,
        ];

        try {
            $stats['schools'] = \App\Models\School::query()->where('status', 'approved')->count();
            $stats['volunteers'] = \App\Models\Volunteer::query()
                ->where('status', 'approved')
                ->where('show_on_website', true)
                ->count();
            $stats['lessons'] = \App\Models\Lesson::query()->where('status', 'published')->count();
            $stats['donations'] = \App\Models\Donation::query()->whereIn('status', ['done', 'succeeded'])->count();
        } catch (\Throwable $exception) {
            // Fallback placeholders keep homepage stable if counters are not available.
        }
    @endphp

    <section class="relative overflow-hidden rounded-3xl bg-[#0f172a] text-white">
        <div class="absolute -left-12 -top-12 h-52 w-52 rounded-full bg-[#1d8cf8]/35 blur-3xl"></div>
        <div class="absolute -bottom-16 right-0 h-64 w-64 rounded-full bg-[#1d8cf8]/35 blur-3xl"></div>

        <div class="relative grid gap-8 p-6 sm:p-8 lg:grid-cols-12 lg:items-center lg:p-10 xl:p-12">
            <div class="lg:col-span-7">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/75">Hour of Light</p>
                <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl">
                    Lighting Up Learning
                    <span class="block text-[#9bc9ff]">for Underserved Schools</span>
                </h1>
                <p class="mt-5 max-w-2xl text-sm leading-7 text-white/90 sm:text-base">
                    Hour of Light partners with schools to strengthen teaching quality, improve systems, and upgrade classrooms with practical EdTech support so every child can access better learning opportunities.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ $donateFundsUrl }}" class="inline-flex items-center justify-center rounded-xl bg-[#1d8cf8] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#0b79e4] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                        Donate Funds
                    </a>
                    <a href="{{ $donateDevicesUrl }}" class="inline-flex items-center justify-center rounded-xl border border-white/35 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                        Donate Devices
                    </a>
                    <a href="{{ $donateLessonUrl }}" class="inline-flex items-center justify-center rounded-xl border border-[#9bc9ff]/80 bg-[#1d8cf8]/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#1d8cf8]/35 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#9bc9ff]">
                        Donate One Hour
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="overflow-hidden rounded-2xl border border-white/20 bg-white/10 p-2 backdrop-blur-sm">
                    <img src="{{ asset('images/home-hero.jpg') }}" alt="Students and teachers collaborating in a classroom supported by Hour of Light" class="h-72 w-full rounded-xl object-cover sm:h-80 lg:h-[430px]">
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 rounded-3xl bg-white p-5 shadow-sm sm:p-7">
        <h2 class="sr-only">Impact Stats</h2>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl border border-black/10 bg-[#f7f8f4] p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Partner Schools</p>
                <p class="mt-2 text-3xl font-extrabold text-black">{{ $stats['schools'] > 0 ? number_format($stats['schools']) : '12+' }}</p>
                <p class="mt-1 text-xs text-slate-600">Schools connected to our mission</p>
            </article>
            <article class="rounded-2xl border border-black/10 bg-[#f7f8f4] p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Volunteer Teachers</p>
                <p class="mt-2 text-3xl font-extrabold text-black">{{ $stats['volunteers'] > 0 ? number_format($stats['volunteers']) : '80+' }}</p>
                <p class="mt-1 text-xs text-slate-600">Educators donating their expertise</p>
            </article>
            <article class="rounded-2xl border border-black/10 bg-[#f7f8f4] p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Published Lessons</p>
                <p class="mt-2 text-3xl font-extrabold text-black">{{ $stats['lessons'] > 0 ? number_format($stats['lessons']) : '250+' }}</p>
                <p class="mt-1 text-xs text-slate-600">Learning resources shared with schools</p>
            </article>
            <article class="rounded-2xl border border-black/10 bg-[#f7f8f4] p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Verified Contributions</p>
                <p class="mt-2 text-3xl font-extrabold text-black">{{ $stats['donations'] > 0 ? number_format($stats['donations']) : '100+' }}</p>
                <p class="mt-1 text-xs text-slate-600">Support actions helping classrooms grow</p>
            </article>
        </div>
        <p class="mt-4 text-xs text-slate-600">Live values appear when records are available; placeholders are shown otherwise.</p>
    </section>

    <section class="mt-6 rounded-3xl bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#1d8cf8]">What We Do</p>
                <h2 class="mt-2 text-3xl font-extrabold text-black sm:text-4xl">Programs Built for Real Classroom Impact</h2>
            </div>
            <p class="max-w-xl text-sm leading-6 text-slate-700">Each initiative is designed for practical execution, sustainable support, and measurable change in learning quality.</p>
        </div>

        <div class="mt-7 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <article class="group rounded-2xl border border-black/10 bg-gradient-to-b from-white to-[#f7f8f4] p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1d8cf8]">01</p>
                <h3 class="mt-2 text-lg font-extrabold text-black">Teacher Training (Lesson Planning)</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">We train teachers to plan stronger lessons, teach with clarity, and assess learning effectively using practical tools that work in real classrooms.</p>
            </article>
            <article class="group rounded-2xl border border-black/10 bg-gradient-to-b from-white to-[#f7f8f4] p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1d8cf8]">02</p>
                <h3 class="mt-2 text-lg font-extrabold text-black">School Management Systems (SMS)</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">We help schools move from manual records to simple digital systems for attendance, exams, fee tracking, and reporting.</p>
            </article>
            <article class="group rounded-2xl border border-black/10 bg-gradient-to-b from-white to-[#f7f8f4] p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1d8cf8]">03</p>
                <h3 class="mt-2 text-lg font-extrabold text-black">Timetabling Support</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">We implement timetabling tools and train school staff to build conflict-free schedules and reduce administrative burden.</p>
            </article>
            <article class="group rounded-2xl border border-black/10 bg-gradient-to-b from-white to-[#f7f8f4] p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1d8cf8]">04</p>
                <h3 class="mt-2 text-lg font-extrabold text-black">Adopt a Classroom (EdTech Upgrade)</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">We adopt classrooms and support schools to set up EdTech-ready learning spaces, including guidance on devices, display setup, and teacher readiness.</p>
            </article>
            <article class="group rounded-2xl border border-black/10 bg-gradient-to-b from-white to-[#f7f8f4] p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1d8cf8]">05</p>
                <h3 class="mt-2 text-lg font-extrabold text-black">Donate Devices (Laptops/PCs)</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">Individuals and organizations can donate used laptops and PCs. We refurbish and repurpose devices for deserving schools.</p>
            </article>
            <article class="group rounded-2xl border border-[#1d8cf8]/30 bg-gradient-to-b from-[#eef6ff] to-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-bold uppercase tracking-wider text-[#1d8cf8]">06</p>
                <h3 class="mt-2 text-lg font-extrabold text-black">Donate One Hour (Model Lessons)</h3>
                <p class="mt-2 text-sm leading-6 text-slate-700">Qualified teachers donate one hour of a model lesson, recorded or live. Lessons are shared with students in underprivileged schools to strengthen concept understanding and exposure to quality teaching.</p>
                <a href="{{ $donateLessonUrl }}" class="mt-4 inline-flex items-center justify-center rounded-lg bg-[#1d8cf8] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#0b79e4] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]">
                    Donate Your Lesson Now
                </a>
            </article>
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-3">
        <article class="rounded-3xl border border-black/10 bg-white p-6 shadow-sm lg:col-span-2">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#1d8cf8]">How You Can Help</p>
            <h2 class="mt-2 text-3xl font-extrabold text-black sm:text-4xl">Choose the Support Path That Fits You</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-[#f7f8f4] p-4">
                    <h3 class="text-sm font-extrabold uppercase tracking-wide text-black">Donate Funds</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-700">Fund training, implementation, and classroom upgrades for schools with limited resources.</p>
                    <a href="{{ $donateFundsUrl }}" class="mt-3 inline-flex items-center text-sm font-bold text-[#1d8cf8] underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]">Contribute Funds</a>
                </div>
                <div class="rounded-2xl bg-[#f7f8f4] p-4">
                    <h3 class="text-sm font-extrabold uppercase tracking-wide text-black">Donate Devices</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-700">Give laptops, PCs, and classroom hardware a second life in underserved schools.</p>
                    <a href="{{ $donateDevicesUrl }}" class="mt-3 inline-flex items-center text-sm font-bold text-[#1d8cf8] underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]">Donate Hardware</a>
                </div>
                <div class="rounded-2xl bg-[#f7f8f4] p-4">
                    <h3 class="text-sm font-extrabold uppercase tracking-wide text-black">Donate One Hour</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-700">Record or deliver a model lesson to amplify quality teaching where it is needed most.</p>
                    <a href="{{ $donateLessonUrl }}" class="mt-3 inline-flex items-center text-sm font-bold text-[#1d8cf8] underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]">Become a Teaching Volunteer</a>
                </div>
            </div>
        </article>

        <article class="rounded-3xl bg-[#1d4ed8] p-6 text-white shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-white/80">Trusted Education Mission</p>
            <h2 class="mt-2 text-2xl font-extrabold leading-tight">Aligned with SDG 4: Quality Education for All</h2>
            <p class="mt-4 text-sm leading-7 text-white/95">Hour of Light supports the global education agenda by helping schools build sustainable systems, teacher capacity, and technology readiness for long-term learning outcomes.</p>
            <ul class="mt-5 space-y-2 text-sm text-white/95">
                <li class="rounded-xl bg-white/10 px-3 py-2">Evidence-based support for underserved communities</li>
                <li class="rounded-xl bg-white/10 px-3 py-2">Practical school systems that improve daily operations</li>
                <li class="rounded-xl bg-white/10 px-3 py-2">Teacher-first approach to lasting classroom impact</li>
            </ul>
        </article>
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



