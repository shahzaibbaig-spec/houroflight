@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-4xl rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-extrabold text-black">Volunteer Dashboard</h1>
        <p class="mt-2 text-sm text-slate-600">Welcome to your teacher volunteer space.</p>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-black/10 bg-[#efefe7] p-4">
                <p class="text-xs font-bold uppercase text-slate-500">Profile Summary</p>
                <p class="mt-2 text-sm text-black"><span class="font-semibold">Name:</span> {{ $user->name }}</p>
                <p class="mt-1 text-sm text-black"><span class="font-semibold">Email:</span> {{ $user->email }}</p>
                <p class="mt-1 text-sm text-black">
                    <span class="font-semibold">Subjects:</span>
                    {{ $user->volunteer->expertise_subjects ?? 'Not provided yet' }}
                </p>
                <p class="mt-1 text-sm text-black">
                    <span class="font-semibold">Grade Levels:</span>
                    {{ $user->volunteer->grade_levels ?? 'Not provided yet' }}
                </p>
            </div>

            <div class="rounded-xl border border-[#1d8cf8]/20 bg-[#eef6ff] p-4">
                <p class="text-xs font-bold uppercase text-slate-500">Volunteer Status</p>
                <p class="mt-2 inline-flex rounded-full bg-white px-3 py-1 text-xs font-bold uppercase text-[#1d8cf8]">
                    {{ $user->volunteer->status ?? 'pending' }}
                </p>
                <p class="mt-3 text-sm text-slate-700">Your lesson will be reviewed before publishing.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('volunteer.profile.edit') }}" class="inline-flex items-center rounded-xl border border-[#1d8cf8]/40 bg-white px-4 py-2 text-sm font-semibold text-[#1d8cf8]">
                        Update Profile
                    </a>
                    <a href="{{ route('volunteer.lessons.create') }}" class="hol-btn-secondary">
                        Upload Lecture
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
