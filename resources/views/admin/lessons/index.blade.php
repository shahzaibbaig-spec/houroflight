@extends('layouts.app')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-extrabold text-black">Lesson Moderation</h1>
        <p class="mt-2 text-sm text-slate-600">Review and manage volunteer lessons.</p>

        <form method="GET" action="{{ route('admin.lessons.index') }}" class="mt-4 grid gap-3 sm:grid-cols-3">
            <div>
                <label class="hol-label">Status</label>
                <select name="status" class="hol-input">
                    <option value="">All statuses</option>
                    @foreach($allowedStatuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="hol-label">Subject</label>
                <select name="subject" class="hol-input">
                    <option value="">All subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject }}" @selected(request('subject') === $subject)>{{ $subject }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="hol-btn-secondary">Apply</button>
                <a href="{{ route('admin.lessons.index') }}" class="inline-flex items-center justify-center rounded-xl border border-black/15 px-4 py-3 text-sm font-semibold text-slate-900">Reset</a>
            </div>
        </form>
    </section>

    @if(session('success'))
        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('lesson'))
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('lesson') }}
        </div>
    @endif

    <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] border-collapse">
                <thead>
                    <tr class="bg-[#efefe7]">
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Status</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Title</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Subject</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Grades</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Teacher</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Updated</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lessons as $lesson)
                        <tr>
                            <td class="border border-black/10 px-3 py-2 text-sm font-semibold uppercase">{{ $lesson->status }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $lesson->title }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $lesson->subject }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $lesson->grade_min }} - {{ $lesson->grade_max }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $lesson->user->name ?? '-' }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $lesson->updated_at?->format('Y-m-d H:i') }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">
                                <a href="{{ route('admin.lessons.show', $lesson) }}" class="inline-flex items-center rounded-lg border border-[#1d8cf8]/40 px-3 py-2 text-xs font-bold text-[#1d8cf8]">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-black/10 px-3 py-6 text-center text-sm text-slate-600">No lessons found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $lessons->links() }}
        </div>
    </section>
@endsection

