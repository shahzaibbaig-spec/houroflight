<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use Illuminate\View\View;

class VolunteerPublicController extends Controller
{
    public function index(): View
    {
        $volunteers = Volunteer::query()
            ->with(['user', 'documents'])
            ->where('show_on_website', true)
            ->where('status', 'approved')
            ->whereHas('user', function ($query) {
                $query->where('role', 'volunteer_teacher');
            })
            ->orderByDesc('id')
            ->get();

        return view('volunteers.index', compact('volunteers'));
    }

    public function show(Volunteer $volunteer): View
    {
        $volunteer->load(['user', 'lessons', 'documents']);

        if (
            ! $volunteer->show_on_website
            || $volunteer->status !== 'approved'
            || ! $volunteer->user
            || $volunteer->user->role !== 'volunteer_teacher'
        ) {
            abort(404);
        }

        $lessons = $volunteer->lessons()
            ->where('status', 'published')
            ->latest('published_at')
            ->get();

        return view('volunteers.show', compact('volunteer', 'lessons'));
    }
}
