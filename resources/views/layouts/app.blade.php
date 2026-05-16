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
    @php
        $user = auth()->user();
        $isVolunteerTeacher = $user && $user->role === 'volunteer_teacher';
        $isAdmin = $user && $user->role === 'admin';
        $canViewDonations = $user && in_array($user->role, ['donor', 'admin'], true);
        $secondaryCtaUrl = auth()->check() ? url('/register-school') : route('signup');
        $secondaryCtaLabel = auth()->check() ? 'Register School' : 'Volunteer';
    @endphp

    <header class="hol-header">
        <div class="hol-nav-shell">
            <div class="hol-nav-row">
                <a href="{{ url('/') }}" class="hol-brand">Hour of Light</a>

                <nav class="hol-desktop-nav" aria-label="Primary">
                    <a href="{{ url('/') }}" class="hol-top-link">Home</a>
                    <a href="{{ url('/about-us') }}" class="hol-top-link">About Us</a>
                    <a href="{{ route('schools.partners') }}" class="hol-top-link">Our Partner Schools</a>
                    <a href="{{ route('volunteers.index') }}" class="hol-top-link">Our Volunteers</a>
                    <a href="{{ route('lessons.library') }}" class="hol-top-link">Lesson Library</a>
                    <a href="{{ route('e-resources.index') }}" class="hol-top-link">E-Resources</a>
                    <a href="{{ route('ai.lesson-planner') }}" class="hol-top-link">AI Planner</a>
                    <a href="{{ url('/register-school') }}" class="hol-top-link">Register School</a>

                    @if($canViewDonations)
                        <a href="{{ route('donor.donations') }}" class="hol-top-link">My Donations</a>
                    @endif

                    @if($isVolunteerTeacher)
                        <a href="{{ route('volunteer.dashboard') }}" class="hol-top-link">Volunteer Panel</a>
                        <a href="{{ route('volunteer.profile.edit') }}" class="hol-top-link">My Profile</a>
                        <a href="{{ route('volunteer.lessons.index') }}" class="hol-top-link">My Lectures</a>
                        <a href="{{ route('teacher.courses.index') }}" class="hol-top-link">My Courses</a>
                        <a href="{{ route('teacher.certificates.index') }}" class="hol-top-link">My Certificates</a>
                    @endif

                    @if($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="hol-top-link">Admin</a>
                        <a href="{{ route('admin.volunteers.index') }}" class="hol-top-link">Admin: Volunteers</a>
                        <a href="{{ route('admin.lessons.index') }}" class="hol-top-link">Admin: Lessons</a>
                    @endif
                </nav>

                <div class="hol-nav-actions">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="hol-top-link hol-auth-btn">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hol-top-link hol-auth-btn">Login</a>
                        <a href="{{ route('signup') }}" class="hol-top-link hol-auth-btn">Sign Up</a>
                    @endauth

                    <a href="{{ $secondaryCtaUrl }}" class="hol-cta-secondary">{{ $secondaryCtaLabel }}</a>
                    <a href="{{ route('donate.form') }}" class="hol-cta-primary">Donate</a>
                </div>

                <button
                    type="button"
                    class="hol-mobile-toggle"
                    id="hol-mobile-toggle"
                    aria-expanded="false"
                    aria-controls="hol-mobile-menu"
                    aria-label="Open navigation menu"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

        <div id="hol-mobile-menu" class="hol-mobile-menu hidden">
            <nav class="hol-mobile-panel" aria-label="Mobile Primary">
                <a href="{{ url('/') }}" class="hol-nav-link">Home</a>
                <a href="{{ url('/about-us') }}" class="hol-nav-link">About Us</a>
                <a href="{{ route('schools.partners') }}" class="hol-nav-link">Our Partner Schools</a>
                <a href="{{ route('volunteers.index') }}" class="hol-nav-link">Our Volunteers</a>
                <a href="{{ route('lessons.library') }}" class="hol-nav-link">Lesson Library</a>
                <a href="{{ route('e-resources.index') }}" class="hol-nav-link">E-Resources</a>
                <a href="{{ route('ai.lesson-planner') }}" class="hol-nav-link">AI Planner</a>
                <a href="{{ url('/register-school') }}" class="hol-nav-link">Register School</a>

                @if($canViewDonations)
                    <a href="{{ route('donor.donations') }}" class="hol-nav-link">My Donations</a>
                @endif

                @if($isVolunteerTeacher)
                    <a href="{{ route('volunteer.dashboard') }}" class="hol-nav-link">Volunteer Panel</a>
                    <a href="{{ route('volunteer.profile.edit') }}" class="hol-nav-link">My Profile</a>
                    <a href="{{ route('volunteer.lessons.index') }}" class="hol-nav-link">My Lectures</a>
                    <a href="{{ route('teacher.courses.index') }}" class="hol-nav-link">My Courses</a>
                    <a href="{{ route('teacher.certificates.index') }}" class="hol-nav-link">My Certificates</a>
                @endif

                @if($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="hol-nav-link">Admin</a>
                    <a href="{{ route('admin.volunteers.index') }}" class="hol-nav-link">Admin: Volunteers</a>
                    <a href="{{ route('admin.lessons.index') }}" class="hol-nav-link">Admin: Lessons</a>
                @endif

                <div class="hol-mobile-cta-wrap">
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="hol-nav-link w-full text-left">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hol-nav-link">Login</a>
                        <a href="{{ route('signup') }}" class="hol-nav-link">Sign Up</a>
                    @endauth
                    <a href="{{ $secondaryCtaUrl }}" class="hol-cta-secondary w-full justify-center">{{ $secondaryCtaLabel }}</a>
                    <a href="{{ route('donate.form') }}" class="hol-cta-primary w-full justify-center">Donate</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="hol-content">
        <main class="hol-main">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <footer class="hol-footer">
            <div class="hol-footer-shell">
                <div class="hol-footer-grid">
                    <section>
                        <h2 class="hol-footer-title">Hour of Light</h2>
                        <p class="hol-footer-copy">We support underserved schools by strengthening teaching quality, improving school systems, and advancing practical EdTech readiness for lasting student impact.</p>
                    </section>

                    <section>
                        <h2 class="hol-footer-title">Quick Links</h2>
                        <ul class="hol-footer-links">
                            <li><a href="{{ url('/') }}">Home</a></li>
                            <li><a href="{{ url('/about-us') }}">About Us</a></li>
                            <li><a href="{{ route('schools.partners') }}">Our Partner Schools</a></li>
                            <li><a href="{{ route('volunteers.index') }}">Our Volunteers</a></li>
                            <li><a href="{{ route('lessons.library') }}">Lesson Library</a></li>
                            <li><a href="{{ route('e-resources.index') }}">E-Resources</a></li>
                            <li><a href="{{ route('donate.form') }}">Donate</a></li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="hol-footer-title">Suggestions? Questions?</h2>
                        <p class="hol-footer-copy">We&apos;d love to hear from schools, educators, and supporters around the world.</p>
                        <a href="mailto:info@houroflight.com" class="hol-footer-contact">info@houroflight.com</a>
                        <a href="{{ url('/register-school') }}" class="hol-footer-contact">Register Your School</a>
                    </section>
                </div>

                <div class="hol-footer-bottom">
                    <p>&copy; {{ date('Y') }} Hour of Light. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        (() => {
            const toggle = document.getElementById('hol-mobile-toggle');
            const menu = document.getElementById('hol-mobile-menu');
            if (!toggle || !menu) return;

            const closeMenu = () => {
                menu.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
            };

            const openMenu = () => {
                menu.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
            };

            toggle.addEventListener('click', () => {
                const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                if (isOpen) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

            menu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', closeMenu);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeMenu();
                }
            });
        })();
    </script>
</body>
</html>
