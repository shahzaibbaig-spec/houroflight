<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVolunteerRequest;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(StoreVolunteerRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isVolunteerTeacher = $request->boolean('is_volunteer_teacher');

        $user = DB::transaction(function () use ($validated, $isVolunteerTeacher) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $isVolunteerTeacher ? User::ROLE_VOLUNTEER_TEACHER : User::ROLE_DONOR,
                'is_teacher' => $isVolunteerTeacher,
            ]);

            if ($isVolunteerTeacher) {
                Volunteer::create([
                    'user_id' => $user->id,
                    'expertise_subjects' => $validated['expertise_subjects'],
                    'grade_levels' => $validated['grade_levels'],
                    'availability' => $validated['availability'],
                    'lesson_format' => $validated['lesson_format'],
                    'years_experience' => $validated['years_experience'],
                    'short_bio' => $validated['short_bio'],
                    'status' => 'pending',
                ]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('donate.form');
    }
}
