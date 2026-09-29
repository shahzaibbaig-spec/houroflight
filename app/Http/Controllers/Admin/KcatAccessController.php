<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Nsms\Kcat\Models\SchoolClass;
use Nsms\Kcat\Models\Student;

class KcatAccessController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['search' => ['nullable', 'string', 'max:255']]);
        $search = trim($validated['search'] ?? '');
        $accounts = DB::table('kcat_access')
            ->join('users', 'users.id', '=', 'kcat_access.user_id')
            ->leftJoin('kcat_students', 'kcat_students.user_id', '=', 'users.id')
            ->leftJoin('kcat_school_classes', 'kcat_school_classes.id', '=', 'kcat_students.class_id')
            ->select('users.id', 'users.name', 'users.email', 'kcat_access.role',
                'kcat_students.student_id', 'kcat_school_classes.name as class_name', 'kcat_school_classes.section')
            ->when($search !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('users.name', 'like', '%'.$search.'%')->orWhere('users.email', 'like', '%'.$search.'%')))
            ->orderBy('users.name')->paginate(20)->withQueryString();

        return view('admin.kcat.access', compact('accounts', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', Rule::in(['student', 'counselor', 'principal'])],
            'student_id' => ['nullable', 'required_if:role,student', 'string', 'max:100'],
            'grade' => ['nullable', 'required_if:role,student', 'integer', 'between:7,12'],
            'section' => ['nullable', 'string', 'max:30'],
        ]);
        $user = User::where('email', $data['email'])->firstOrFail();
        if ($user->role === User::ROLE_ADMIN) {
            return back()->withErrors(['email' => 'Website admins already manage KCAT. Choose another account.'])->withInput();
        }
        if ($data['role'] === 'student') {
            $request->validate([
                'student_id' => [Rule::unique('kcat_students', 'student_id')->ignore($user->id, 'user_id')],
            ]);
        }

        DB::transaction(function () use ($data, $user): void {
            if ($data['role'] === 'student') {
                $class = SchoolClass::firstOrCreate([
                    'name' => 'Class '.$data['grade'], 'section' => $data['section'] ?? '',
                ]);
                Student::updateOrCreate(['user_id' => $user->id], [
                    'student_id' => $data['student_id'], 'name' => $user->name,
                    'class_id' => $class->id, 'status' => 'active',
                ]);
            } else {
                Student::where('user_id', $user->id)->update(['status' => 'inactive']);
            }
            DB::table('kcat_access')->updateOrInsert(['user_id' => $user->id], ['role' => $data['role']]);
        });

        return redirect()->route('admin.kcat.access.index')->with('success', 'KCAT access saved for '.$user->name.'.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->role === User::ROLE_ADMIN, 422, 'Website admins have automatic KCAT access.');
        DB::transaction(function () use ($user): void {
            DB::table('kcat_access')->where('user_id', $user->id)->delete();
            Student::where('user_id', $user->id)->update(['status' => 'inactive']);
        });

        return back()->with('success', 'Assigned role removed. Public tests and personal reports remain available.');
    }
}
