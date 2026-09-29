<?php
namespace Nsms\Kcat\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Nsms\Kcat\Models\SchoolClass;
use Nsms\Kcat\Models\Student;

class LinkStudent extends Command
{
    protected $signature = 'kcat:student {email} {student_id} {--class=Class 8} {--section=}';
    protected $description = 'Link an existing website login to a KCAT student';
    public function handle(): int
    {
        $user = config('auth.providers.users.model')::where('email', $this->argument('email'))->first();
        if (! $user) { $this->error('Create this account through the website first.'); return self::FAILURE; }
        if (Student::where('student_id', $this->argument('student_id'))->where('user_id', '!=', $user->id)->exists()) {
            $this->error('That student identifier is already linked to another login.'); return self::FAILURE;
        }
        DB::transaction(function () use ($user): void {
            $class = SchoolClass::firstOrCreate(['name' => $this->option('class'), 'section' => $this->option('section') ?? '']);
            $student = Student::updateOrCreate(['user_id' => $user->id], ['student_id' => $this->argument('student_id'),
                'name' => $user->name, 'class_id' => $class->id, 'status' => 'active']);
            $this->info('Linked KCAT student database ID: '.$student->id);
        });
        $this->info('Grant access separately with: php artisan kcat:grant '.$user->email.' student');
        return self::SUCCESS;
    }
}
