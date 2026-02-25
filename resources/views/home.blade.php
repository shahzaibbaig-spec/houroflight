@extends('layouts.app')

@section('content')
    <section class="overflow-hidden rounded-2xl bg-[#ff4d00] text-white">
        <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-2 lg:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Hour of Light</p>
                <h1 class="mt-3 text-4xl font-extrabold leading-none sm:text-6xl">Upgrade<br>Facilities</h1>
                <p class="mt-4 max-w-md text-sm text-white/90 sm:text-base">
                    We help schools build modern learning spaces through teacher development, EdTech support, and classroom adoption programs.
                </p>
            </div>
            <img src="{{ asset('images/home-hero.jpg') }}" alt="Students and teachers collaborating" class="h-full max-h-[360px] w-full rounded-2xl object-cover">
        </div>
    </section>

    <section class="mt-6 grid gap-6 md:grid-cols-2">
        <article class="hol-panel">
            <h2 class="text-3xl font-extrabold leading-tight">Adopting classrooms<br>for impact</h2>
            <p class="mt-3 text-sm text-slate-700">
                Each adopted classroom gets the right tools, structured implementation, and educator support to improve learning outcomes.
            </p>
            <a href="{{ url('/register-school') }}" class="hol-btn-primary mt-5">Register a School</a>
        </article>
        <article class="hol-panel bg-[#1d8cf8] text-white">
            <h2 class="text-3xl font-extrabold leading-tight">Train teachers,<br>scale change</h2>
            <p class="mt-3 text-sm text-white/90">
                From lesson planning to timetable software and school management systems, we focus on practical, high-value training.
            </p>
            <a href="{{ url('/donate') }}" class="hol-btn-secondary mt-5 border border-white/20 bg-white text-[#1d8cf8] hover:bg-white/90">Donate Support</a>
        </article>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-3">
        <img src="{{ asset('design/values.png') }}" alt="Team values" class="h-48 w-full rounded-2xl object-cover">
        <img src="{{ asset('design/roles.png') }}" alt="Team roles" class="h-48 w-full rounded-2xl object-cover">
        <video class="h-48 w-full rounded-2xl object-cover" autoplay loop muted playsinline><source src="{{ asset('videos/Logo_Animation_Correction_and_GIF.mp4') }}" type="video/mp4"></video>
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


