<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Hour of Light' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hol-shell min-h-screen antialiased">
    <aside class="hol-sidebar">
        <a href="{{ url('/') }}" class="hol-brand">Hour of Light</a>

        <nav class="hol-nav">
            <a href="{{ url('/') }}" class="hol-nav-link">Home</a>
            <a href="{{ url('/about-us') }}" class="hol-nav-link">About Us</a>
            <a href="{{ route('schools.partners') }}" class="hol-nav-link">Our Partner Schools</a>
            <a href="{{ route('volunteers.index') }}" class="hol-nav-link">Our Volunteers</a>
            <a href="{{ route('lessons.library') }}" class="hol-nav-link">Lesson Library</a>
            <a href="{{ route('ai.lesson-planner') }}" class="hol-nav-link">AI Planner</a>
            <a href="{{ url('/register-school') }}" class="hol-nav-link">Register School</a>
            @auth
                @if(in_array(auth()->user()->role, ['donor', 'admin'], true))
                    <a href="{{ route('donor.donations') }}" class="hol-nav-link">My Donations</a>
                @endif
                @if(auth()->user()->role === 'volunteer_teacher')
                    <a href="{{ route('volunteer.dashboard') }}" class="hol-nav-link">Volunteer Panel</a>
                    <a href="{{ route('volunteer.profile.edit') }}" class="hol-nav-link">My Profile</a>
                    <a href="{{ route('volunteer.lessons.index') }}" class="hol-nav-link">My Lectures</a>
                    <a href="{{ route('teacher.courses.index') }}" class="hol-nav-link">My Courses</a>
                    <a href="{{ route('teacher.certificates.index') }}" class="hol-nav-link">My Certificates</a>
                @endif
                @if(auth()->user()->role === 'admin')
                    <details class="w-full" @if(request()->routeIs('admin.*')) open @endif>
                        <summary class="hol-nav-link cursor-pointer list-none text-center">Admin Panel</summary>
                        <div class="mt-1 space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="hol-nav-link ps-4">Admin</a>
                            <a href="{{ route('admin.volunteers.index') }}" class="hol-nav-link ps-4">Admin: Volunteers</a>
                            <a href="{{ route('admin.lessons.index') }}" class="hol-nav-link ps-4">Admin: Lessons</a>
                        </div>
                    </details>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hol-nav-link w-full text-left">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hol-nav-link">Login</a>
                <a href="{{ route('signup') }}" class="hol-nav-link">Sign Up</a>
            @endauth
            <a href="{{ route('donate.form') }}" class="hol-nav-link hol-nav-link-primary">Donate</a>
        </nav>
    </aside>

    <div class="hol-content">
        <main class="hol-main">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <footer class="hol-footer">
            <div class="hol-footer-inner">
                <p class="text-2xl font-bold leading-none">Suggestions?<br>Questions?</p>
                <p class="text-right font-medium">&copy; {{ date('Y') }} Hour of Light</p>
            </div>
        </footer>
    </div>
</body>
</html>
