@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-2xl rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-extrabold text-black">Create Account</h1>
        <p class="mt-2 text-sm text-slate-600">Sign up as a donor or volunteer teacher to support Hour of Light.</p>

        @if($errors->any())
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.perform') }}" class="mt-5 space-y-4">
            @csrf

            <div>
                <label for="name" class="hol-label">Full Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="hol-input">
            </div>

            <div>
                <label for="email" class="hol-label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="hol-input">
            </div>

            <div>
                <label for="password" class="hol-label">Password</label>
                <input id="password" name="password" type="password" required class="hol-input">
            </div>

            <div>
                <label for="password_confirmation" class="hol-label">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="hol-input">
            </div>

            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-800">
                <input id="is_volunteer_teacher" name="is_volunteer_teacher" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" {{ old('is_volunteer_teacher') ? 'checked' : '' }}>
                I want to register as a volunteer teacher
            </label>

            <div id="volunteer-fields" class="space-y-4 rounded-xl border border-[#1d8cf8]/20 bg-[#eef6ff] p-4 {{ old('is_volunteer_teacher') ? '' : 'hidden' }}">
                <div>
                    <label for="expertise_subjects" class="hol-label">Subjects Expertise</label>
                    <input id="expertise_subjects" name="expertise_subjects" type="text" value="{{ old('expertise_subjects') }}" class="hol-input" placeholder="Math, Science, English">
                </div>

                <div>
                    <label for="grade_levels" class="hol-label">Grade Levels</label>
                    <input id="grade_levels" name="grade_levels" type="text" value="{{ old('grade_levels') }}" class="hol-input" placeholder="Grade 4-8">
                </div>

                <div>
                    <label for="years_experience" class="hol-label">Years of Experience</label>
                    <input id="years_experience" name="years_experience" type="number" min="0" max="80" value="{{ old('years_experience') }}" class="hol-input">
                </div>

                <div>
                    <label for="lesson_format" class="hol-label">Lesson Format</label>
                    <select id="lesson_format" name="lesson_format" class="hol-input">
                        <option value="">Select format</option>
                        <option value="recorded" {{ old('lesson_format') === 'recorded' ? 'selected' : '' }}>Recorded</option>
                        <option value="live" {{ old('lesson_format') === 'live' ? 'selected' : '' }}>Live</option>
                        <option value="both" {{ old('lesson_format') === 'both' ? 'selected' : '' }}>Both</option>
                    </select>
                </div>

                <div>
                    <label for="availability" class="hol-label">Availability</label>
                    <input id="availability" name="availability" type="text" value="{{ old('availability') }}" class="hol-input" placeholder="Weekends, evenings">
                </div>

                <div>
                    <label for="short_bio" class="hol-label">Short Bio</label>
                    <textarea id="short_bio" name="short_bio" rows="4" class="hol-input" placeholder="Tell us about your teaching background.">{{ old('short_bio') }}</textarea>
                </div>
            </div>

            <button type="submit" class="hol-btn-primary w-full">Create Account</button>
        </form>

        <p class="mt-4 text-center text-sm text-slate-700">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-[#1d8cf8] hover:underline">Login</a>
        </p>
    </section>

    <script>
        (() => {
            const checkbox = document.getElementById('is_volunteer_teacher');
            const fields = document.getElementById('volunteer-fields');
            if (!checkbox || !fields) return;
            const sync = () => fields.classList.toggle('hidden', !checkbox.checked);
            checkbox.addEventListener('change', sync);
            sync();
        })();
    </script>
@endsection

