<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class VolunteerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing('volunteer');

        return view('volunteer.dashboard', compact('user'));
    }

    public function submitLesson(): View
    {
        return view('volunteer.submit-lesson');
    }
}

