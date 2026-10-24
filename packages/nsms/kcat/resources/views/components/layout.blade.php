@component('layouts.app', ['title' => 'KCAT | Hour of Light'])
    <section class="kcat-workspace">
    <script>
        window.KCAT = {
            escapeHtml(value) { const el = document.createElement('span'); el.textContent = String(value ?? ''); return el.innerHTML; },
            debounce(fn, delay) { let timer; return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); }; }
        };
    </script>
    <nav class="hol-panel mb-6 flex flex-wrap items-center gap-4 text-sm font-semibold print:hidden" aria-label="KCAT navigation">
        @if(\Illuminate\Support\Facades\Route::has('home'))<a href="{{ route('home') }}" class="font-semibold">Hour of Light</a>@endif
        @if(\Illuminate\Support\Facades\Route::has('kcat.index'))<a href="{{ route('kcat.index') }}">Take KCAT &amp; my reports</a>@endif
        @if(auth()->user()?->role === 'admin' && \Illuminate\Support\Facades\Route::has('admin.kcat.access.index'))
            <a href="{{ route('admin.kcat.access.index') }}">Student &amp; staff access</a>
        @endif
        @can('kcat.view_kcat_panel')
            <a href="{{ route('kcat.career-counselor.kcat.dashboard') }}">Dashboard</a>
            <a href="{{ route('kcat.career-counselor.kcat.tests.index') }}">Tests</a>
            <a href="{{ route('kcat.career-counselor.kcat.assignments.index') }}">Assignments</a>
            <a href="{{ route('kcat.career-counselor.kcat.attempts.index') }}">Results</a>
            <a href="{{ route('kcat.career-counselor.kcat.question-quality.index') }}">Question quality</a>
        @endcan
        @can('kcat.view_all_kcat_reports')<a href="{{ route('kcat.principal.kcat.reports.index') }}">All reports</a>@endcan
        @can('kcat.attempt_kcat_test')<a href="{{ route('kcat.student.kcat.assignments.index') }}">My tests</a>@endcan
    </nav>
    <header class="mb-6">{{ $header ?? '' }}</header>
    <div>
        @if($errors->any())<div role="alert" class="mx-auto mt-4 max-w-5xl rounded-xl bg-red-50 p-4 text-red-800">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        {{ $slot }}
    </div>
    </section>
@endcomponent
