<?php
namespace Nsms\Kcat\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Nsms\Kcat\Models\Student;

class StudentResolver
{
    public function resolveStudentForUser(Authenticatable $user): ?Student
    {
        if (config('kcat.self_service', false)) {
            $student = Student::firstOrCreate(['user_id' => $user->getAuthIdentifier()], [
                'student_id' => 'HOL-'.\Illuminate\Support\Str::uuid(),
                'name' => $user->name, 'status' => 'active',
            ]);
            if ($student->status !== 'active' || $student->name !== $user->name) {
                $student->update(['status' => 'active', 'name' => $user->name]);
            }
            return $student;
        }
        return Student::where('user_id', $user->getAuthIdentifier())->where('status', 'active')->first();
    }
}
