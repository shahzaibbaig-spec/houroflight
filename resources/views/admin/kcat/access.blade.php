@extends('layouts.app')

@section('content')
    <section class="hol-panel">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div><h1 class="text-3xl font-extrabold">KCAT staff &amp; class assignments</h1><p class="mt-2 text-sm text-slate-600">Everyone with a website account can take the public KCAT test and download their report. Use this page to grant staff roles or organize students into classes.</p></div>
            <a class="hol-cta-primary" href="{{ route('kcat.career-counselor.kcat.dashboard') }}">Manage assessments</a>
        </div>
        @if(session('success'))<p role="status" class="mt-4 rounded-xl bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</p>@endif
        @if($errors->any())
            <div role="alert" class="mt-4 rounded-xl bg-red-50 p-4 text-red-800"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <h2 class="mt-6 text-xl font-bold">Add a staff role or class details</h2>
        <p class="mt-2 text-sm text-slate-600">Use an account registered on the website, or create one from the <a class="underline" href="{{ route('admin.dashboard') }}">Admin Panel</a>. To update access, enter the same email again.</p>
        <form method="POST" action="{{ route('admin.kcat.access.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
            @csrf
            <div><label class="hol-label" for="kcat-email">Account email</label><input class="hol-input" id="kcat-email" name="email" type="email" required value="{{ old('email') }}"></div>
            <div><label class="hol-label" for="kcat-role">KCAT role</label><select class="hol-input" id="kcat-role" name="role"><option value="student" @selected(old('role', 'student') === 'student')>Student — take assigned tests</option><option value="counselor" @selected(old('role') === 'counselor')>Counselor — manage assessments</option><option value="principal" @selected(old('role') === 'principal')>Principal — view reports</option></select></div>
            <fieldset id="kcat-student-fields" class="grid gap-4 sm:col-span-2 sm:grid-cols-3">
                <legend class="mb-2 text-sm font-bold">Student details</legend>
                <div><label class="hol-label" for="kcat-student-id">Student ID</label><input class="hol-input" id="kcat-student-id" name="student_id" maxlength="100" value="{{ old('student_id') }}"></div>
                <div><label class="hol-label" for="kcat-grade">Grade</label><select class="hol-input" id="kcat-grade" name="grade">@foreach(range(7, 12) as $grade)<option value="{{ $grade }}" @selected((int) old('grade', 7) === $grade)>Grade {{ $grade }}</option>@endforeach</select></div>
                <div><label class="hol-label" for="kcat-section">Section (optional)</label><input class="hol-input" id="kcat-section" name="section" maxlength="30" value="{{ old('section') }}"></div>
            </fieldset>
            <div class="sm:col-span-2"><button class="hol-cta-primary" type="submit">Save access</button></div>
        </form>
    </section>
    <section class="hol-panel mt-6">
        <h2 class="text-xl font-bold">Assigned access</h2>
        <form method="GET" class="my-4 flex flex-wrap items-end gap-3"><div><label class="hol-label" for="account-search">Search accounts</label><input class="hol-input" id="account-search" name="search" value="{{ $search }}" placeholder="Name or email"></div><button class="hol-cta-secondary" type="submit">Search</button></form>
        <div class="overflow-x-auto"><table class="w-full text-left text-sm">
            <thead><tr class="border-b"><th class="p-3">Account</th><th class="p-3">Role</th><th class="p-3">Student details</th><th class="p-3">Access</th></tr></thead>
            <tbody>@forelse($accounts as $account)
                <tr class="border-b"><td class="p-3"><p class="font-bold">{{ $account->name }}</p>{{ $account->email }}</td><td class="p-3">{{ ucfirst($account->role) }}</td><td class="p-3">@if($account->role === 'student'){{ $account->student_id }}<br>{{ $account->class_name }} {{ $account->section }}@else — @endif</td><td class="p-3"><form method="POST" action="{{ route('admin.kcat.access.destroy', $account->id) }}">@csrf @method('DELETE')<button class="font-semibold text-red-700 underline" type="submit" aria-label="Remove KCAT access for {{ $account->name }}">Remove access</button></form></td></tr>
            @empty<tr><td colspan="4" class="p-4 text-slate-600">No matching KCAT accounts.</td></tr>@endforelse</tbody>
        </table></div>
        <div class="mt-4">{{ $accounts->links() }}</div>
    </section>
    <script>
        (() => {
            const role = document.getElementById('kcat-role');
            const fields = document.getElementById('kcat-student-fields');
            const update = () => {
                const student = role.value === 'student';
                fields.hidden = !student;
                fields.disabled = !student;
                document.getElementById('kcat-student-id').required = student;
            };
            role.addEventListener('change', update);
            update();
        })();
    </script>
@endsection
