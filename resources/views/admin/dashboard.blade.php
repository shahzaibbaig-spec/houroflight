@extends('layouts.app')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-3xl font-extrabold text-black">Admin Panel</h1>
        <p class="mt-2 text-sm text-slate-600">Manage school registrations, donations, and popup announcements.</p>

        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="mb-0 list-disc ps-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <article class="hol-panel">
            <p class="text-xs font-bold uppercase text-slate-500">Schools Registered</p>
            <p class="mt-2 text-3xl font-extrabold text-black">{{ $schools->count() }}</p>
        </article>
        @forelse($donationTotals as $total)
            <article class="hol-panel">
                <p class="text-xs font-bold uppercase text-slate-500">Donations ({{ $total->currency }})</p>
                <p class="mt-2 text-3xl font-extrabold text-black">{{ number_format((float) $total->total_amount, 2) }}</p>
                <p class="mt-1 text-xs text-slate-600">{{ $total->total_count }} records</p>
            </article>
        @empty
            <article class="hol-panel sm:col-span-2">
                <p class="text-sm text-slate-600">No successful donations yet.</p>
            </article>
        @endforelse
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-2">
        <article class="hol-panel">
            <h2 class="text-2xl font-extrabold text-black">Add New User</h2>
            <p class="mt-2 text-sm text-slate-600">Create accounts and assign user type.</p>

            <form method="POST" action="{{ route('admin.users.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="hol-label" for="user_name">Name</label>
                    <input id="user_name" name="name" type="text" class="hol-input" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="hol-label" for="user_email">Email</label>
                    <input id="user_email" name="email" type="email" class="hol-input" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label class="hol-label" for="user_role">User Type</label>
                    <select id="user_role" name="role" class="hol-input" required>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        <option value="donor" @selected(old('role') === 'donor')>Donor</option>
                        <option value="volunteer_teacher" @selected(old('role') === 'volunteer_teacher')>Volunteer Teacher</option>
                        <option value="volunteer_general" @selected(old('role') === 'volunteer_general')>Volunteer General</option>
                    </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="hol-label" for="user_password">Password</label>
                        <input id="user_password" name="password" type="password" class="hol-input" required>
                    </div>
                    <div>
                        <label class="hol-label" for="user_password_confirmation">Confirm Password</label>
                        <input id="user_password_confirmation" name="password_confirmation" type="password" class="hol-input" required>
                    </div>
                </div>
                <button type="submit" class="hol-btn-primary">Create User</button>
            </form>
        </article>

        <article class="hol-panel">
            <h2 class="text-2xl font-extrabold text-black">Create Popup Announcement</h2>
            <p class="mt-2 text-sm text-slate-600">You can post text-only, full-screen media, or YouTube autoplay popups.</p>

            <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" id="timezone_offset_minutes" name="timezone_offset_minutes" value="0">
                <div>
                    <label class="hol-label" for="title">Title (optional)</label>
                    <input id="title" name="title" type="text" class="hol-input">
                </div>
                <div>
                    <label class="hol-label" for="message_html">Message (HTML allowed)</label>
                    <textarea id="message_html" name="message_html" rows="5" class="hol-input" placeholder="You can include text, image tags, video tags, or iframe YouTube embed HTML."></textarea>
                </div>
                <div>
                    <label class="hol-label" for="media_type">Media Type</label>
                    <select id="media_type" name="media_type" class="hol-input">
                        <option value="none">Text only</option>
                        <option value="image">Image (full popup)</option>
                        <option value="video">Video file (full popup)</option>
                        <option value="youtube">YouTube URL (autoplay)</option>
                    </select>
                </div>
                <div>
                    <label class="hol-label" for="media_file">Upload Image/Video</label>
                    <input id="media_file" name="media_file" type="file" class="hol-input" accept="image/*,video/*">
                </div>
                <div>
                    <label class="hol-label" for="youtube_url">YouTube URL (for media type YouTube)</label>
                    <input id="youtube_url" name="youtube_url" type="url" class="hol-input" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="hol-label" for="start_at">Start Time</label>
                        <input id="start_at" name="start_at" type="datetime-local" class="hol-input">
                    </div>
                    <div>
                        <label class="hol-label" for="end_at">End Time</label>
                        <input id="end_at" name="end_at" type="datetime-local" class="hol-input">
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="autoplay" value="1" checked class="h-4 w-4 rounded border-slate-300">
                    Autoplay media
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300">
                    Active now
                </label>

                <button type="submit" class="hol-btn-primary">Save Announcement</button>
            </form>
            <p class="mt-3 text-xs text-slate-500">Tip: Start/End times are interpreted in your browser's local timezone.</p>
        </article>

        <article class="hol-panel">
            <h2 class="text-2xl font-extrabold text-black">Announcements</h2>
            <div class="mt-4 space-y-3">
                @forelse($announcements as $announcement)
                    <div class="rounded-xl border border-black/10 bg-white p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-extrabold text-black">{{ $announcement->title ?: 'Untitled Announcement' }}</p>
                                <p class="mt-1 text-xs text-slate-600">
                                    Type: {{ $announcement->media_type }} |
                                    Active: {{ $announcement->is_active ? 'Yes' : 'No' }} |
                                    Start: {{ optional($announcement->start_at)->format('Y-m-d H:i') ?: '-' }} |
                                    End: {{ optional($announcement->end_at)->format('Y-m-d H:i') ?: '-' }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('admin.announcements.toggle', $announcement) }}">
                                @csrf
                                <button type="submit" class="rounded-lg border border-black/15 bg-white px-3 py-2 text-xs font-bold uppercase text-slate-900">
                                    {{ $announcement->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">No announcements created yet.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-2xl font-extrabold text-black">Users</h2>
        <p class="mt-2 text-sm text-slate-600">Update user passwords from here.</p>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[760px] border-collapse">
                <thead>
                    <tr class="bg-[#efefe7]">
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Name</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Email</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Type</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Password</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $user->name }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $user->email }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $user->role }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">
                                <form method="POST" action="{{ route('admin.users.password.update', $user) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                    @csrf
                                    @method('PUT')
                                    <input type="password" name="password" class="hol-input" placeholder="New password" required>
                                    <input type="password" name="password_confirmation" class="hol-input" placeholder="Confirm password" required>
                                    <button type="submit" class="rounded-lg bg-black px-3 py-2 text-xs font-bold text-white whitespace-nowrap">Update</button>
                                </form>
                            </td>
                            <td class="border border-black/10 px-3 py-2 text-sm">
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user account?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-black/10 px-3 py-4 text-center text-sm text-slate-600">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-2xl font-extrabold text-black">Cash Donation Verifications</h2>
        <div class="mt-4 table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Donor</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Proof</th>
                        <th>Verification</th>
                        <th>Receipt Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proofDonations as $donation)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $donation->donor_name }}</div>
                                <div class="small text-muted">{{ $donation->donor_email }}</div>
                            </td>
                            <td>{{ number_format((float) $donation->amount, 2) }} {{ $donation->currency }}</td>
                            <td>{{ $donation->donation_type }}</td>
                            <td>
                                @if($donation->payment_proof_path)
                                    <a href="{{ asset('storage/'.$donation->payment_proof_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        View Proof
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($donation->verified_at)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($donation->verified_at)
                                    <div><strong>Receipt Number:</strong> {{ $donation->receipt_number ?: '-' }}</div>
                                    <div><strong>Receipt Sent Date:</strong> {{ optional($donation->receipt_sent_at)->format('Y-m-d H:i') ?: '-' }}</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    @if(!$donation->verified_at)
                                        <form method="POST" action="{{ route('admin.donations.verify', $donation) }}">
                                            @csrf
                                            <button class="btn btn-success">Verify Payment</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.donations.approve-proof', $donation) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.donations.cancel-proof', $donation) }}" onsubmit="return confirm('Cancel and delete this donation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No donation proofs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-2xl font-extrabold text-black">Registered Schools</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[720px] border-collapse">
                <thead>
                    <tr class="bg-[#efefe7]">
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">School</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">City</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Principal</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Email</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Needs</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Status</th>
                        <th class="border border-black/10 px-3 py-2 text-left text-xs font-bold uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        <tr>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $school->school_name }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $school->city ?: '-' }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $school->principal_name }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $school->contact_email }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ is_array($school->needs) ? implode(', ', $school->needs) : '-' }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">{{ $school->status }}</td>
                            <td class="border border-black/10 px-3 py-2 text-sm">
                                <form method="POST" action="{{ route('admin.schools.remove', $school) }}" onsubmit="return confirm('Remove this school from partner schools?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-black/10 px-3 py-4 text-center text-sm text-slate-600">No schools registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <script>
        (() => {
            const tz = document.getElementById('timezone_offset_minutes');
            if (tz) {
                tz.value = String(new Date().getTimezoneOffset());
            }
        })();
    </script>
@endsection
