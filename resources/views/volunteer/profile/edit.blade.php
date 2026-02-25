@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-5xl rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h1 class="text-3xl font-extrabold text-black">Volunteer Teacher Profile</h1>
                <p class="mt-1 text-sm text-slate-600">Manage your profile, documents, and visibility on website.</p>
            </div>
            <a href="{{ route('volunteer.lessons.create') }}" class="hol-btn-secondary">Upload Lecture</a>
        </div>

        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('volunteer.profile.update') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 sm:grid-cols-2">
            @csrf
            @method('PUT')

            <div class="sm:col-span-2">
                <label class="hol-label" for="profile_photo">Profile Picture</label>
                <input id="profile_photo" name="profile_photo" type="file" accept="image/*" class="hol-input">
                @if($volunteer->profile_photo_path)
                    <div class="mt-2 flex items-center gap-3">
                        <img src="{{ asset('storage/'.$volunteer->profile_photo_path) }}" alt="Profile photo" class="h-16 w-16 rounded-full object-cover">
                        <span class="text-xs text-slate-600">Current profile picture</span>
                    </div>
                @endif
            </div>

            <div>
                <label class="hol-label" for="expertise_subjects">Subjects & Expertise</label>
                <textarea id="expertise_subjects" name="expertise_subjects" rows="3" class="hol-input">{{ old('expertise_subjects', $volunteer->expertise_subjects) }}</textarea>
            </div>

            <div>
                <label class="hol-label" for="grade_levels">Grade Levels</label>
                <input id="grade_levels" name="grade_levels" type="text" value="{{ old('grade_levels', $volunteer->grade_levels) }}" class="hol-input">
            </div>

            <div>
                <label class="hol-label" for="years_experience">Years of Experience</label>
                <input id="years_experience" name="years_experience" type="number" min="0" max="80" value="{{ old('years_experience', $volunteer->years_experience) }}" class="hol-input">
            </div>

            <div>
                <label class="hol-label" for="lesson_format">Lecture Format</label>
                <select id="lesson_format" name="lesson_format" class="hol-input">
                    <option value="">Select</option>
                    <option value="recorded" @selected(old('lesson_format', $volunteer->lesson_format) === 'recorded')>Recorded</option>
                    <option value="live" @selected(old('lesson_format', $volunteer->lesson_format) === 'live')>Live</option>
                    <option value="both" @selected(old('lesson_format', $volunteer->lesson_format) === 'both')>Both</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="hol-label" for="availability">Availability</label>
                <input id="availability" name="availability" type="text" value="{{ old('availability', $volunteer->availability) }}" class="hol-input">
            </div>

            <div class="sm:col-span-2">
                <label class="hol-label" for="short_bio">Short Bio</label>
                <textarea id="short_bio" name="short_bio" rows="4" class="hol-input">{{ old('short_bio', $volunteer->short_bio) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="hol-label" for="teaching_profile_notes">Teaching Profile (additional details)</label>
                <textarea id="teaching_profile_notes" name="teaching_profile_notes" rows="4" class="hol-input">{{ old('teaching_profile_notes', $volunteer->teaching_profile_notes) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="hol-label" for="degree_details">Degree Details</label>
                <textarea id="degree_details" name="degree_details" rows="3" class="hol-input">{{ old('degree_details', $volunteer->degree_details) }}</textarea>
            </div>

            <div>
                <label class="hol-label" for="degree_document">Upload Degree Document</label>
                <input id="degree_document" name="degree_document" type="file" accept=".pdf,.doc,.docx" class="hol-input">
                @if($volunteer->degree_document_path)
                    <a href="{{ asset('storage/'.$volunteer->degree_document_path) }}" target="_blank" class="mt-1 inline-block text-xs font-semibold text-[#1d8cf8]">View current degree file</a>
                @endif
            </div>

            <div>
                <label class="hol-label" for="certificates_document">Upload Certificates / Awards Document</label>
                <input id="certificates_document" name="certificates_document" type="file" accept=".pdf,.doc,.docx" class="hol-input">
                @if($volunteer->certificates_document_path)
                    <a href="{{ asset('storage/'.$volunteer->certificates_document_path) }}" target="_blank" class="mt-1 inline-block text-xs font-semibold text-[#1d8cf8]">View current certificates file</a>
                @endif
            </div>

            <div class="sm:col-span-2">
                <label class="hol-label" for="certificates_documents">Upload Certificates / Awards (Multiple Files)</label>
                <input id="certificates_documents" name="certificates_documents[]" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" class="hol-input" multiple>
                <p class="mt-1 text-xs text-slate-600">Allowed: PDF, DOC, DOCX, JPG, PNG, WEBP (you can select multiple files).</p>

                @if($volunteer->documents->isNotEmpty())
                    <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($volunteer->documents as $document)
                            <div class="rounded-xl border border-black/10 p-2">
                                @if(str_starts_with((string) $document->mime_type, 'image/'))
                                    <img src="{{ asset('storage/'.$document->file_path) }}" alt="{{ $document->original_name }}" class="h-28 w-full rounded-lg object-cover">
                                @else
                                    <div class="flex h-28 items-center justify-center rounded-lg bg-[#efefe7] text-xs font-bold text-slate-700">
                                        {{ strtoupper(pathinfo($document->original_name, PATHINFO_EXTENSION)) ?: 'FILE' }}
                                    </div>
                                @endif
                                <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="mt-2 block truncate text-xs font-semibold text-[#1d8cf8]">
                                    {{ $document->original_name }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="sm:col-span-2">
                <label class="hol-label" for="awards">Awards / Achievements</label>
                <textarea id="awards" name="awards" rows="3" class="hol-input">{{ old('awards', $volunteer->awards) }}</textarea>
            </div>

            <div class="rounded-xl border border-black/10 bg-[#efefe7] p-4 sm:col-span-2">
                <p class="text-sm font-extrabold text-black">Website Visibility</p>
                <div class="mt-3 flex flex-wrap gap-6">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-800">
                        <input type="checkbox" name="show_photo_on_website" value="1" class="h-4 w-4" {{ old('show_photo_on_website', $volunteer->show_photo_on_website) ? 'checked' : '' }}>
                        Show my picture on website
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-800">
                        <input type="checkbox" name="show_on_website" value="1" class="h-4 w-4" {{ old('show_on_website', $volunteer->show_on_website) ? 'checked' : '' }}>
                        Show my profile on "Our Volunteers" page
                    </label>
                </div>
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="hol-btn-primary">Save Profile</button>
            </div>
        </form>
    </section>
@endsection
