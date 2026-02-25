<?php

namespace App\Http\Controllers;

use App\Mail\SchoolRegistrationAdminNotification;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SchoolRegistrationController extends Controller
{
    public function partners(): View
    {
        $schools = School::query()
            ->latest()
            ->get();

        return view('schools.partners', compact('schools'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'principal_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:120'],
            'school_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'needs' => ['required', 'array', 'min:1'],
            'needs.*' => ['string', 'max:255'],
        ]);

        if ($request->hasFile('school_logo')) {
            $validated['logo_path'] = $request->file('school_logo')->store('schools/logos', 'public');
        }

        unset($validated['school_logo']);

        $school = School::create($validated);

        try {
            Mail::to([
                'infor@houroflight.com',
                'shahzaib.baig@gmail.com',
            ])->send(new SchoolRegistrationAdminNotification($school));

            return back()->with('success', 'School registration submitted successfully.');
        } catch (\Throwable $exception) {
            Log::warning('School registration email failed to send.', [
                'school_id' => $school->id,
                'error' => $exception->getMessage(),
            ]);

            return back()->with('success', 'School registered successfully. Email notification is temporarily unavailable.');
        }
    }
}
