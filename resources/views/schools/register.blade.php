@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-4xl">
        <div class="overflow-hidden rounded-2xl bg-[#1d8cf8] p-6 text-white sm:p-8">
            <h1 class="text-4xl font-extrabold leading-none">Register School</h1>
            <p class="mt-3 max-w-2xl text-sm text-white/90">Share your school details and select support areas. We will review and connect with your leadership team.</p>
        </div>

        <div class="hol-panel mt-4">
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <p class="font-medium">Please fix the following:</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url('/register-school') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="school_name" class="hol-label">School Name</label>
                        <input id="school_name" name="school_name" type="text" value="{{ old('school_name') }}" required class="hol-input">
                    </div>

                    <div>
                        <label for="principal_name" class="hol-label">Principal Name</label>
                        <input id="principal_name" name="principal_name" type="text" value="{{ old('principal_name') }}" required class="hol-input">
                    </div>

                    <div>
                        <label for="contact_email" class="hol-label">Email</label>
                        <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email') }}" required class="hol-input">
                    </div>

                    <div>
                        <label for="phone_number" class="hol-label">Phone</label>
                        <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number') }}" required class="hol-input">
                    </div>

                    <div>
                        <label for="city" class="hol-label">City</label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}" required class="hol-input">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="hol-label">Address</label>
                        <textarea id="address" name="address" rows="4" required class="hol-input">{{ old('address') }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="school_logo" class="hol-label">School Logo (Optional)</label>
                        <input id="school_logo" name="school_logo" type="file" accept=".jpg,.jpeg,.png,.webp,image/*" class="hol-input">
                        <p class="mt-1 text-xs text-slate-600">Accepted formats: JPG, PNG, WEBP. Max size: 5MB.</p>
                    </div>
                </div>

                <div>
                    <p class="hol-label">Services Needed</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @php
                            $serviceOptions = [
                                'Teacher Training (Lesson Planning)',
                                'School Management System',
                                'Timetable Software Training',
                                'Adopt a Classroom (EdTech Upgrade)',
                            ];
                        @endphp

                        @foreach ($serviceOptions as $service)
                            <label class="flex items-start gap-3 rounded-xl border border-black/10 bg-[#efefe7] px-3 py-3 text-sm font-medium text-slate-800">
                                <input
                                    type="checkbox"
                                    name="needs[]"
                                    value="{{ $service }}"
                                    class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#1d8cf8] focus:ring-[#1d8cf8]"
                                    @checked(in_array($service, old('needs', []), true))
                                >
                                <span>{{ $service }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="hol-btn-primary">Submit Registration</button>
            </form>
        </div>
    </section>
@endsection
