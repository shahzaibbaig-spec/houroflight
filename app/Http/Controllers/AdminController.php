<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Donation;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $schools = School::query()->latest()->get();
        $users = User::query()->latest()->get();
        $donationTotals = Donation::query()
            ->whereIn('status', ['succeeded', 'done'])
            ->selectRaw('currency, SUM(amount) as total_amount, COUNT(*) as total_count')
            ->groupBy('currency')
            ->get();
        $proofDonations = Donation::query()
            ->whereNotNull('payment_proof_path')
            ->latest()
            ->get();

        $announcements = Announcement::query()->latest()->get();

        return view('admin.dashboard', compact('schools', 'users', 'donationTotals', 'announcements', 'proofDonations'));
    }

    public function storeAnnouncement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'message_html' => ['nullable', 'string'],
            'media_type' => ['required', 'in:none,image,video,youtube'],
            'media_file' => ['nullable', 'file', 'max:51200', 'mimes:jpeg,jpg,png,webp,mp4,mov,webm'],
            'youtube_url' => ['nullable', 'url', 'max:500'],
            'autoplay' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'timezone_offset_minutes' => ['nullable', 'integer'],
        ]);

        $startAt = $validated['start_at'] ?? null;
        $endAt = $validated['end_at'] ?? null;
        $offset = (int) ($validated['timezone_offset_minutes'] ?? 0);
        if ($startAt) {
            $startAt = Carbon::parse($startAt)->addMinutes($offset);
        }
        if ($endAt) {
            $endAt = Carbon::parse($endAt)->addMinutes($offset);
        }

        $mediaPath = null;
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
            $directory = public_path('uploads/announcements');
            if (! is_dir($directory)) {
                mkdir($directory, 0775, true);
            }
            $file->move($directory, $filename);
            $mediaPath = 'uploads/announcements/'.$filename;
        }

        Announcement::create([
            'created_by' => $request->user()->id,
            'title' => $validated['title'] ?? null,
            'message_html' => $validated['message_html'] ?? null,
            'media_type' => $validated['media_type'],
            'media_path' => $mediaPath,
            'youtube_url' => $validated['youtube_url'] ?? null,
            'autoplay' => (bool) ($validated['autoplay'] ?? true),
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Announcement created successfully.');
    }

    public function toggleAnnouncement(Announcement $announcement): RedirectResponse
    {
        $announcement->update([
            'is_active' => ! $announcement->is_active,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Announcement status updated.');
    }

    public function approveDonationProof(Donation $donation): RedirectResponse
    {
        if ($donation->status !== 'pending' || ! $donation->payment_proof_path) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Donation is not in a reviewable pledge state.');
        }

        $donation->update([
            'status' => 'done',
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Payment proof approved and donation marked as done.');
    }

    public function cancelDonationProof(Donation $donation): RedirectResponse
    {
        if ($donation->payment_proof_path) {
            Storage::disk('public')->delete($donation->payment_proof_path);
        }

        $donation->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Donation canceled and deleted.');
    }

    public function removeSchool(School $school): RedirectResponse
    {
        if ($school->logo_path) {
            Storage::disk('public')->delete($school->logo_path);
        }

        $school->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'School removed from partner listings.');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in($this->allowedRoles())],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $role = (string) $validated['role'];

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $role,
            'is_teacher' => $role === User::ROLE_VOLUNTEER_TEACHER,
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'User created successfully.');
    }

    public function updateUserPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Password updated for '.$user->email.'.');
    }

    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'You cannot delete your own account.');
        }

        $userEmail = $user->email;
        $user->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'User deleted: '.$userEmail.'.');
    }

    /**
     * @return array<int, string>
     */
    protected function allowedRoles(): array
    {
        return [
            User::ROLE_ADMIN,
            User::ROLE_DONOR,
            User::ROLE_VOLUNTEER_TEACHER,
            User::ROLE_VOLUNTEER_GENERAL,
        ];
    }
}
