@extends('layouts.app')

@section('content')
    <section class="hol-panel">
        <p class="text-sm font-bold uppercase text-blue-600">Free assessment · Instant report</p>
        <h1 class="mt-2 text-3xl font-extrabold">KORT Cognitive Assessment Test</h1>
        <p class="mt-4 max-w-3xl text-slate-600">Explore your verbal, quantitative, non-verbal, and spatial reasoning. Create an account, take the test, and download your personal report as a PDF.</p>
        <p class="mt-3 text-sm text-slate-600">Designed for Grades 7–12. Your results include section scores, areas to practise, and subject stream suggestions.</p>
        @guest
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('signup') }}" class="hol-cta-primary">Sign up and take KCAT</a>
                <a href="{{ route('kcat.login') }}" class="hol-cta-secondary">Already registered? Sign in</a>
            </div>
        @endguest
        @auth
            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                <a href="{{ route('kcat.student.kcat.assignments.index') }}" class="font-semibold text-blue-700 underline">My assigned tests</a>
                @can('kcat.view_kcat_panel')<a href="{{ route('kcat.career-counselor.kcat.dashboard') }}" class="font-semibold text-blue-700 underline">Manage assessments</a>@endcan
                @can('kcat.view_all_kcat_reports')<a href="{{ route('kcat.principal.kcat.reports.index') }}" class="font-semibold text-blue-700 underline">All reports</a>@endcan
            </div>
        @endauth
    </section>
    <section class="mt-6 grid gap-4 sm:grid-cols-2">
        @forelse($tests as $test)
            @php($previous = $attempts->firstWhere('kcat_test_id', $test->id))
            <article class="hol-panel">
                <h2 class="text-xl font-bold">{{ $test->title }}</h2>
                <p class="mt-3 text-sm text-slate-600">{{ $test->questions_count }} questions · Four reasoning areas · {{ $test->duration_minutes }} minutes suggested</p>
                <p class="mt-3 text-sm text-slate-600">Work at your own pace. You can return to an unfinished test. Your report is ready as soon as you submit.</p>
                @auth
                    @if($previous && in_array($previous->status, ['submitted', 'reviewed'], true))
                        <a class="hol-cta-primary mt-4 inline-flex" href="{{ route('kcat.student.kcat.attempts.result', $previous) }}">View my report</a>
                    @else
                        <form class="mt-4" method="POST" action="{{ route('kcat.start', $test) }}">@csrf<button class="hol-cta-primary" type="submit">{{ $previous ? 'Continue KCAT' : 'Start KCAT' }}</button></form>
                    @endif
                @endauth
            </article>
        @empty
            <p class="hol-panel text-slate-600">The assessment is temporarily unavailable. Please check back shortly.</p>
        @endforelse
    </section>
    @if($attempts->isNotEmpty())
        <section class="hol-panel mt-6">
            <h2 class="text-xl font-bold">My tests and reports</h2>
            <div class="mt-4 space-y-4">
                @foreach($attempts as $attempt)
                    <article class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 py-4">
                        <div><h3 class="font-bold">{{ $attempt->test?->title }}</h3><p class="text-sm text-slate-600">{{ ucfirst(str_replace('_', ' ', $attempt->status)) }}</p></div>
                        <div class="flex flex-wrap gap-3">
                            @if(in_array($attempt->status, ['submitted', 'reviewed'], true))
                                <a class="hol-cta-secondary" href="{{ route('kcat.student.kcat.attempts.result', $attempt) }}">View report</a>
                                <a class="hol-cta-primary" href="{{ route('kcat.report.download', $attempt) }}">Download PDF</a>
                            @elseif($attempt->status === 'in_progress')
                                <a class="hol-cta-primary" href="{{ $attempt->is_adaptive ? route('kcat.student.kcat.attempts.adaptive.next', $attempt) : route('kcat.student.kcat.attempts.question', [$attempt, 0]) }}">Continue test</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@endsection