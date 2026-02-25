@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-xl rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-extrabold text-black">Login</h1>
        <p class="mt-2 text-sm text-slate-600">Login to use the AI Lesson Planner services.</p>

        @if($errors->any())
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.perform') }}" class="mt-5 space-y-4">
            @csrf
            <div>
                <label for="login" class="hol-label">Username or Email</label>
                <input id="login" name="login" type="text" value="{{ old('login') }}" required class="hol-input">
            </div>
            <div>
                <label for="password" class="hol-label">Password</label>
                <input id="password" name="password" type="password" required class="hol-input">
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300">
                Remember me
            </label>
            <button type="submit" class="hol-btn-primary w-full">Login</button>
        </form>

        <p class="mt-4 text-center text-sm text-slate-700">
            Don&apos;t have an account?
            <a href="{{ route('signup') }}" class="font-semibold text-[#1d8cf8] hover:underline">Sign up</a>
        </p>
    </section>
@endsection
