<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateVolunteerProfileRequest;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\VolunteerDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VolunteerProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $volunteer = Volunteer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'expertise_subjects' => '',
                'grade_levels' => '',
                'lesson_format' => 'both',
                'status' => 'pending',
                'show_photo_on_website' => true,
                'show_on_website' => true,
            ]
        );

        $volunteer->load('documents');

        return view('volunteer.profile.edit', compact('user', 'volunteer'));
    }

    public function update(UpdateVolunteerProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $volunteer = Volunteer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'expertise_subjects' => '',
                'grade_levels' => '',
                'lesson_format' => 'both',
                'status' => 'pending',
                'show_photo_on_website' => true,
                'show_on_website' => true,
            ]
        );

        $volunteer->load('documents');

        $validated = $request->validated();

        if ($request->hasFile('profile_photo')) {
            if ($volunteer->profile_photo_path) {
                Storage::disk('public')->delete($volunteer->profile_photo_path);
            }
            $validated['profile_photo_path'] = $request->file('profile_photo')->store('volunteers/photos', 'public');
        }

        if ($request->hasFile('degree_document')) {
            if ($volunteer->degree_document_path) {
                Storage::disk('public')->delete($volunteer->degree_document_path);
            }
            $validated['degree_document_path'] = $request->file('degree_document')->store('volunteers/docs', 'public');
        }

        if ($request->hasFile('certificates_document')) {
            if ($volunteer->certificates_document_path) {
                Storage::disk('public')->delete($volunteer->certificates_document_path);
            }
            $validated['certificates_document_path'] = $request->file('certificates_document')->store('volunteers/docs', 'public');
        }

        if ($request->hasFile('certificates_documents')) {
            foreach ($request->file('certificates_documents') as $file) {
                $path = $file->store('volunteers/certificates', 'public');

                VolunteerDocument::create([
                    'volunteer_id' => $volunteer->id,
                    'category' => 'certificate_award',
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        unset($validated['profile_photo'], $validated['degree_document'], $validated['certificates_document'], $validated['certificates_documents']);

        $validated['show_photo_on_website'] = $request->boolean('show_photo_on_website');
        $validated['show_on_website'] = $request->boolean('show_on_website');

        if ($volunteer->status !== 'approved') {
            $validated['status'] = 'pending';
        }

        if ($user->role !== User::ROLE_VOLUNTEER_TEACHER) {
            $user->update([
                'role' => User::ROLE_VOLUNTEER_TEACHER,
                'is_teacher' => true,
            ]);
        }

        $volunteer->update($validated);

        return redirect()
            ->route('volunteer.profile.edit')
            ->with('success', 'Volunteer profile updated successfully.');
    }
}
