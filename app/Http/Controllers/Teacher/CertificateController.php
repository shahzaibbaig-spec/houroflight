<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(): View
    {
        $certificates = Certificate::query()
            ->where('user_id', (int) auth()->id())
            ->with('course')
            ->latest('issued_at')
            ->get();

        return view('teacher.certificates.index', [
            'certificates' => $certificates,
        ]);
    }
}
