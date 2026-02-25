@extends('layouts.app')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-extrabold text-black">Volunteer Profiles</h1>
        <p class="mt-2 text-sm text-slate-600">Approve, edit, or delete teacher volunteer profiles before public display.</p>

        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
    </section>

    <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <form method="GET" class="grid gap-3 sm:grid-cols-3">
            <div>
                <label class="hol-label" for="status">Status</label>
                <select id="status" name="status" class="hol-input">
                    <option value="">All</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                    <option value="approved" @selected($status === 'approved')>Approved</option>
                    <option value="rejected" @selected($status === 'rejected')>Rejected</option>
                </select>
            </div>
            <div>
                <label class="hol-label" for="q">Search</label>
                <input id="q" name="q" type="text" value="{{ $keyword }}" class="hol-input" placeholder="Name, email, subject...">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="hol-btn-primary">Filter</button>
                <a href="{{ route('admin.volunteers.index') }}" class="hol-btn-secondary">Reset</a>
            </div>
        </form>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[860px] border-collapse">
                <thead>
                    <tr class="bg-[#efefe7]">
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Teacher</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Subject</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Experience</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Visibility</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Status</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($volunteers as $volunteer)
                        <tr>
                            <td class="border border-black/10 px-3 py-2 text-sm">
                                <div class="font-semibold text-black">{{ $volunteer->user->name ?? '-' }}</div>
                                <div class="text-xs text-slate-600">{{ $volunteer->user->email ?? '-' }}</div>
                            </td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ \Illuminate\Support\Str::limit($volunteer->expertise_subjects ?: '-', 80) }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $volunteer->years_experience ?? '-' }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">
                                <div>Profile: {{ $volunteer->show_on_website ? 'Yes' : 'No' }}</div>
                                <div>Photo: {{ $volunteer->show_photo_on_website ? 'Yes' : 'No' }}</div>
                            </td>
                            <td class="border border-black/10 px-3 py-2 text-sm">
                                <span class="inline-flex rounded-full bg-[#1d8cf8]/10 px-2 py-1 text-xs font-bold text-[#1d8cf8]">
                                    {{ ucfirst($volunteer->status) }}
                                </span>
                            </td>
                            <td class="border border-black/10 px-3 py-2 text-sm">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.volunteers.edit', $volunteer) }}" class="rounded-lg border border-black/15 bg-white px-2 py-1 text-xs font-bold text-black">Edit</a>
                                    <form method="POST" action="{{ route('admin.volunteers.approve', $volunteer) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-2 py-1 text-xs font-bold text-white">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.volunteers.reject', $volunteer) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-amber-600 px-2 py-1 text-xs font-bold text-white">Reject</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.volunteers.destroy', $volunteer) }}" onsubmit="return confirm('Delete this volunteer profile?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-red-600 px-2 py-1 text-xs font-bold text-white">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-black/10 px-3 py-4 text-center text-sm text-slate-600">No volunteer profiles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $volunteers->links() }}
        </div>
    </section>
@endsection

