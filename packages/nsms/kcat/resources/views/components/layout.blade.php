<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"><title>KCAT | Hour of Light</title>
    <link rel="stylesheet" href="{{ asset('vendor/kcat/kcat.css') }}">
    <script>
        window.KCAT = {
            escapeHtml(value) { const el = document.createElement('span'); el.textContent = String(value ?? ''); return el.innerHTML; },
            debounce(fn, delay) { let timer; return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); }; }
        };
    </script>
</head>
<body class="bg-slate-50 text-slate-900">
    <nav class="flex flex-wrap gap-4 border-b bg-white px-6 py-4 print:hidden" aria-label="KCAT navigation">
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
        @if(\Illuminate\Support\Facades\Route::has('logout'))
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Sign out</button></form>
        @endif
    </nav>
    <header class="border-b bg-white px-6 py-4">{{ $header ?? '' }}</header>
    <main class="px-4">
        @if($errors->any())<div role="alert" class="mx-auto mt-4 max-w-5xl rounded-xl bg-red-50 p-4 text-red-800">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        {{ $slot }}
    </main>
</body>
</html>
