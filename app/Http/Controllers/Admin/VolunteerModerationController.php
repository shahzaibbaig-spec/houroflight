<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VolunteerModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $keyword = trim((string) $request->string('q'));

        $volunteers = Volunteer::query()
            ->with('user')
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('expertise_subjects', 'like', '%'.$keyword.'%')
                        ->orWhere('grade_levels', 'like', '%'.$keyword.'%')
                        ->orWhereHas('user', function ($users) use ($keyword) {
                            $users->where('name', 'like', '%'.$keyword.'%')
                                ->orWhere('email', 'like', '%'.$keyword.'%');
                        });
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.volunteers.index', compact('volunteers', 'status', 'keyword'));
    }

    public function edit(Volunteer $volunteer): View
    {
        $volunteer->load('user');

        return view('admin.volunteers.edit', compact('volunteer'));
    }

    public function update(Request $request, Volunteer $volunteer): RedirectResponse
    {
        $validated = $request->validate([
            'expertise_subjects' => ['required', 'string', 'max:2000'],
            'grade_levels' => ['required', 'string', 'max:255'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:80'],
            'availability' => ['nullable', 'string', 'max:255'],
            'lesson_format' => ['nullable', 'in:recorded,live,both'],
            'short_bio' => ['nullable', 'string', 'max:4000'],
            'teaching_profile_notes' => ['nullable', 'string', 'max:4000'],
            'degree_details' => ['nullable', 'string', 'max:3000'],
            'awards' => ['nullable', 'string', 'max:3000'],
            'show_photo_on_website' => ['nullable', 'boolean'],
            'show_on_website' => ['nullable', 'boolean'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $validated['show_photo_on_website'] = $request->boolean('show_photo_on_website');
        $validated['show_on_website'] = $request->boolean('show_on_website');

        $volunteer->update($validated);

        return redirect()
            ->route('admin.volunteers.index')
            ->with('success', 'Volunteer profile updated.');
    }

    public function approve(Volunteer $volunteer): RedirectResponse
    {
        $volunteer->update(['status' => 'approved']);

        return redirect()
            ->route('admin.volunteers.index')
            ->with('success', 'Volunteer profile approved.');
    }

    public function reject(Volunteer $volunteer): RedirectResponse
    {
        $volunteer->update(['status' => 'rejected']);

        return redirect()
            ->route('admin.volunteers.index')
            ->with('success', 'Volunteer profile rejected.');
    }

    public function destroy(Volunteer $volunteer): RedirectResponse
    {
        $volunteer->delete();

        return redirect()
            ->route('admin.volunteers.index')
            ->with('success', 'Volunteer profile deleted.');
    }
}

