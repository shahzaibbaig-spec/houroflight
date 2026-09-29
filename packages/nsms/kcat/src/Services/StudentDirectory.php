<?php
namespace Nsms\Kcat\Services;

use Nsms\Kcat\Models\Student;
use Illuminate\Support\Collection;

class StudentDirectory
{
    public function searchStudents(string $term): Collection
    {
        if (mb_strlen(trim($term)) < 2) { return collect(); }
        return Student::with('classRoom')->where('status', 'active')
            ->where(fn ($q) => $q->where('name', 'like', '%'.$term.'%')->orWhere('student_id', 'like', '%'.$term.'%'))
            ->orderBy('name')->limit(20)->get()->map(fn (Student $student) => [
                'id' => $student->id, 'name' => $student->name, 'student_id' => $student->student_id,
                'admission_number' => $student->student_id, 'father_name' => $student->father_name,
                'class_section' => trim(($student->classRoom?->name ?? '').' '.($student->classRoom?->section ?? '')),
            ]);
    }
}
