@extends('layouts.app')

@section('content')
    @php
        $subjectOptions = $volunteers
            ->flatMap(function ($volunteer) {
                $subjects = (string) ($volunteer->expertise_subjects ?? '');
                if ($subjects === '') {
                    return [];
                }

                return collect(preg_split('/\s*,\s*/', $subjects, -1, PREG_SPLIT_NO_EMPTY))
                    ->map(fn ($subject) => trim((string) $subject))
                    ->filter();
            })
            ->unique(fn ($subject) => strtolower((string) $subject))
            ->sort()
            ->values();
    @endphp

    <section class="overflow-hidden rounded-3xl bg-[#0f172a] text-white">
        <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-12 lg:items-center lg:p-10">
            <div class="lg:col-span-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Volunteer Directory</p>
                <h1 class="mt-3 text-4xl font-extrabold leading-tight sm:text-5xl">Our Volunteers</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-white/90 sm:text-base">
                    Meet volunteer teachers supporting students through quality lessons. Browse profiles by name or subject and explore each educator&apos;s expertise.
                </p>
            </div>
            <div class="lg:col-span-4">
                <div class="rounded-2xl border border-white/20 bg-white/10 p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.15em] text-white/80">Visible Profiles</p>
                    <p class="mt-2 text-4xl font-extrabold">{{ number_format($volunteers->count()) }}</p>
                    <p class="mt-2 text-sm text-white/85">Approved volunteer teacher profiles currently shown in this directory.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 rounded-3xl bg-white p-5 shadow-sm sm:p-6">
        <h2 class="sr-only">Directory Filters</h2>
        <div class="grid gap-3 md:grid-cols-12 md:items-end">
            <div class="md:col-span-6">
                <label for="directory-search" class="hol-label">Search by name or subject</label>
                <input
                    id="directory-search"
                    type="search"
                    class="hol-input"
                    placeholder="e.g. Ayesha, Science, Mathematics"
                    autocomplete="off"
                >
            </div>
            <div class="md:col-span-4">
                <label for="subject-filter" class="hol-label">Filter by subject</label>
                <select id="subject-filter" class="hol-input">
                    <option value="">All subjects</option>
                    @foreach($subjectOptions as $subject)
                        <option value="{{ strtolower($subject) }}">{{ $subject }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <button
                    type="button"
                    id="clear-filters"
                    class="inline-flex w-full items-center justify-center rounded-xl border border-black/20 bg-white px-4 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
                >
                    Clear
                </button>
            </div>
        </div>
        <p id="directory-results-count" class="mt-3 text-sm text-slate-600" aria-live="polite">
            Showing {{ $volunteers->count() }} volunteer {{ $volunteers->count() === 1 ? 'profile' : 'profiles' }}.
        </p>
    </section>

    @if($volunteers->isNotEmpty())
        <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3" id="volunteer-directory-grid">
            @foreach($volunteers as $volunteer)
                @php
                    $name = trim((string) ($volunteer->user->name ?? 'Volunteer Teacher'));
                    $subjects = trim((string) ($volunteer->expertise_subjects ?? 'Volunteer Teacher'));
                    $subjectsLower = strtolower($subjects);
                    $messagePreview = \Illuminate\Support\Str::limit($volunteer->short_bio ?: ($volunteer->teaching_profile_notes ?: 'Passionate about supporting underserved schools.'), 140);
                    $certificateCount = (int) $volunteer->documents->count();
                    $yearsExperience = $volunteer->years_experience;
                    $initials = collect(explode(' ', preg_replace('/\s+/', ' ', $name)))
                        ->filter()
                        ->take(2)
                        ->map(fn ($part) => strtoupper(substr((string) $part, 0, 1)))
                        ->implode('');
                    if ($initials === '') {
                        $initials = 'VT';
                    }
                @endphp

                <article
                    class="group flex h-full flex-col rounded-2xl border border-black/10 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                    data-volunteer-card
                    data-name="{{ strtolower($name) }}"
                    data-subjects="{{ $subjectsLower }}"
                >
                    <div class="flex items-start gap-3">
                        @if($volunteer->show_photo_on_website && $volunteer->profile_photo_path)
                            <img
                                src="{{ asset('storage/'.$volunteer->profile_photo_path) }}"
                                alt="Profile photo of {{ $name }}"
                                class="h-16 w-16 rounded-full border border-black/10 object-cover"
                            >
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-full border border-[#1d8cf8]/25 bg-[#eef6ff] text-lg font-extrabold text-[#1d8cf8]">
                                {{ $initials }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <a
                                href="{{ route('volunteers.show', $volunteer) }}"
                                class="block truncate text-lg font-extrabold text-black transition hover:text-[#1d8cf8] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]"
                            >
                                {{ $name }}
                            </a>
                            <p class="mt-0.5 text-xs font-medium text-slate-600">{{ $subjects }}</p>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                @if($volunteer->status === 'approved')
                                    <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-700">Approved Profile</span>
                                @endif
                                <span class="inline-flex items-center rounded-full border border-[#1d8cf8]/25 bg-[#eef6ff] px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-[#1d8cf8]">Volunteer Teacher</span>
                                @if($certificateCount > 0)
                                    <span class="inline-flex items-center rounded-full border border-slate-300 bg-slate-100 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-700">Documents on File</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <dl class="mt-4 grid gap-3 text-sm">
                        <div class="rounded-xl bg-[#f7f8f4] px-3 py-2">
                            <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Years of Experience</dt>
                            <dd class="mt-1 font-semibold text-black">{{ $yearsExperience !== null ? $yearsExperience : '-' }}</dd>
                        </div>
                        <div class="rounded-xl bg-[#f7f8f4] px-3 py-2">
                            <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Certificates Submitted</dt>
                            <dd class="mt-1 font-semibold text-black">{{ $certificateCount }}</dd>
                        </div>
                        <div class="rounded-xl bg-[#f7f8f4] px-3 py-2">
                            <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Message Preview</dt>
                            <dd class="mt-1 leading-6 text-slate-800">{{ $messagePreview }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @if($volunteer->degree_document_path)
                            <a
                                href="{{ asset('storage/'.$volunteer->degree_document_path) }}"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex items-center rounded-lg border border-[#1d8cf8]/35 px-3 py-2 text-xs font-bold text-[#1d8cf8] transition hover:bg-[#eef6ff] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]"
                            >
                                Degree
                            </a>
                        @endif
                        @if($volunteer->certificates_document_path)
                            <a
                                href="{{ asset('storage/'.$volunteer->certificates_document_path) }}"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex items-center rounded-lg border border-[#1d8cf8]/35 px-3 py-2 text-xs font-bold text-[#1d8cf8] transition hover:bg-[#eef6ff] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]"
                            >
                                Certificates
                            </a>
                        @endif
                        <a
                            href="{{ route('volunteers.show', $volunteer) }}"
                            class="inline-flex items-center rounded-lg bg-[#1d8cf8] px-3 py-2 text-xs font-bold text-white transition hover:bg-[#0b79e4] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1d8cf8]"
                        >
                            View Full Profile
                        </a>
                    </div>
                </article>
            @endforeach
        </section>

        <section id="directory-empty-filtered" class="mt-6 hidden rounded-2xl border border-dashed border-black/20 bg-white p-8 text-center shadow-sm">
            <h2 class="text-xl font-extrabold text-black">No matching volunteers found</h2>
            <p class="mt-2 text-sm text-slate-600">Try a different name or subject filter to discover more profiles.</p>
        </section>
    @else
        <section class="mt-6 rounded-2xl bg-white p-8 text-center shadow-sm">
            <h2 class="text-2xl font-extrabold text-black">Volunteer Profiles Coming Soon</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">No volunteer profiles are currently available. Please check back again shortly.</p>
        </section>
    @endif

    <script>
        (() => {
            const searchInput = document.getElementById('directory-search');
            const subjectFilter = document.getElementById('subject-filter');
            const clearBtn = document.getElementById('clear-filters');
            const cards = Array.from(document.querySelectorAll('[data-volunteer-card]'));
            const count = document.getElementById('directory-results-count');
            const filteredEmpty = document.getElementById('directory-empty-filtered');
            const grid = document.getElementById('volunteer-directory-grid');

            if (!searchInput || !subjectFilter || !clearBtn || !cards.length || !count || !filteredEmpty || !grid) {
                return;
            }

            const applyFilters = () => {
                const query = searchInput.value.trim().toLowerCase();
                const subject = subjectFilter.value.trim().toLowerCase();
                let visibleCount = 0;

                cards.forEach((card) => {
                    const name = (card.getAttribute('data-name') || '').toLowerCase();
                    const subjects = (card.getAttribute('data-subjects') || '').toLowerCase();
                    const matchesQuery = query === '' || name.includes(query) || subjects.includes(query);
                    const matchesSubject = subject === '' || subjects.includes(subject);
                    const show = matchesQuery && matchesSubject;
                    card.classList.toggle('hidden', !show);
                    if (show) {
                        visibleCount++;
                    }
                });

                count.textContent = `Showing ${visibleCount} volunteer ${visibleCount === 1 ? 'profile' : 'profiles'}.`;
                filteredEmpty.classList.toggle('hidden', visibleCount > 0);
                grid.classList.toggle('hidden', visibleCount === 0);
            };

            searchInput.addEventListener('input', applyFilters);
            subjectFilter.addEventListener('change', applyFilters);
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                subjectFilter.value = '';
                applyFilters();
                searchInput.focus();
            });
        })();
    </script>
@endsection

