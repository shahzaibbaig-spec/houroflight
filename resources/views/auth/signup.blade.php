@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-xl rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-extrabold text-black">Create Account</h1>
        <p class="mt-2 text-sm text-slate-600">Sign up to access AI Lesson Planner services.</p>

        @if($errors->any())
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('signup.perform') }}" class="mt-5 space-y-4">
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
            <button type="submit" class="hol-btn-primary w-full">Create Account</button>
        </form>

        <p class="mt-4 text-center text-sm text-slate-700">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-[#1d8cf8] hover:underline">Login</a>
        </p>
    </section>
@endsection
