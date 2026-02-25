@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-5xl rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-3xl font-extrabold text-black">Edit Volunteer Profile</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $volunteer->user->name ?? '-' }} ({{ $volunteer->user->email ?? '-' }})</p>
            </div>
            <a href="{{ route('admin.volunteers.index') }}" class="hol-btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.volunteers.update', $volunteer) }}" class="mt-5 grid gap-4 sm:grid-cols-2">
            @csrf
            @method('PUT')

            <div class="sm:col-span-2">
                <label class="hol-label" for="expertise_subjects">Subjects & Expertise</label>
                <textarea id="expertise_subjects" name="expertise_subjects" rows="3" class="hol-input">{{ old('expertise_subjects', $volunteer->expertise_subjects) }}</textarea>
            </div>

            <div>
                <label class="hol-label" for="grade_levels">Grade Levels</label>
                <input id="grade_levels" name="grade_levels" type="text" value="{{ old('grade_levels', $volunteer->grade_levels) }}" class="hol-input">
            </div>

            <div>
                <label class="hol-label" for="years_experience">Years Experience</label>
                <input id="years_experience" name="years_experience" type="number" min="0" max="80" value="{{ old('years_experience', $volunteer->years_experience) }}" class="hol-input">
            </div>

            <div>
                <label class="hol-label" for="lesson_format">Lesson Format</label>
                <select id="lesson_format" name="lesson_format" class="hol-input">
                    <option value="">Select</option>
                    <option value="recorded" @selected(old('lesson_format', $volunteer->lesson_format) === 'recorded')>Recorded</option>
                    <option value="live" @selected(old('lesson_format', $volunteer->lesson_format) === 'live')>Live</option>
                    <option value="both" @selected(old('lesson_format', $volunteer->lesson_format) === 'both')>Both</option>
                </select>
            </div>

            <div>
                <label class="hol-label" for="availability">Availability</label>
                <input id="availability" name="availability" type="text" value="{{ old('availability', $volunteer->availability) }}" class="hol-input">
            </div>

            <div class="sm:col-span-2">
                <label class="hol-label" for="short_bio">Short Bio</label>
                <textarea id="short_bio" name="short_bio" rows="4" class="hol-input">{{ old('short_bio', $volunteer->short_bio) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="hol-label" for="teaching_profile_notes">Additional Notes</label>
                <textarea id="teaching_profile_notes" name="teaching_profile_notes" rows="4" class="hol-input">{{ old('teaching_profile_notes', $volunteer->teaching_profile_notes) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="hol-label" for="degree_details">Degree Details</label>
                <textarea id="degree_details" name="degree_details" rows="3" class="hol-input">{{ old('degree_details', $volunteer->degree_details) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="hol-label" for="awards">Awards</label>
                <textarea id="awards" name="awards" rows="3" class="hol-input">{{ old('awards', $volunteer->awards) }}</textarea>
            </div>

            <div>
                <label class="hol-label" for="status">Status</label>
                <select id="status" name="status" class="hol-input">
                    <option value="pending" @selected(old('status', $volunteer->status) === 'pending')>Pending</option>
                    <option value="approved" @selected(old('status', $volunteer->status) === 'approved')>Approved</option>
                    <option value="rejected" @selected(old('status', $volunteer->status) === 'rejected')>Rejected</option>
                </select>
            </div>

            <div class="flex items-end gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-slate-800">
                    <input type="checkbox" name="show_photo_on_website" value="1" class="h-4 w-4" {{ old('show_photo_on_website', $volunteer->show_photo_on_website) ? 'checked' : '' }}>
                    Show Photo
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-800">
                    <input type="checkbox" name="show_on_website" value="1" class="h-4 w-4" {{ old('show_on_website', $volunteer->show_on_website) ? 'checked' : '' }}>
                    Show Profile
                </label>
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="hol-btn-primary">Save Changes</button>
            </div>
        </form>
    </section>
@endsection

