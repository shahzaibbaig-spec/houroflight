<?php
namespace KcatTransferTests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Nsms\Kcat\Models\KcatAssignment;
use Nsms\Kcat\Models\KcatAttempt;
use Nsms\Kcat\Models\KcatTest;
use Nsms\Kcat\Models\SchoolClass;
use Nsms\Kcat\Models\Student;
use Nsms\Kcat\Seeders\KcatDefaultSeeder;

class PortableKcatTest extends TestCase
{
    private TestUser $counselor;
    private TestUser $login;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('users', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('email')->unique(); $table->string('password');
            $table->rememberToken(); $table->timestamps();
        });
        foreach (glob(dirname(__DIR__).'/database/migrations/*.php') as $migration) { (require $migration)->up(); }
        $this->counselor = TestUser::create(['name' => 'Counselor', 'email' => 'counselor@example.test', 'password' => bcrypt('test-password')]);
        $this->login = TestUser::create(['name' => 'Student', 'email' => 'student@example.test', 'password' => bcrypt('test-password')]);
        DB::table('kcat_access')->insert([['user_id' => $this->counselor->id, 'role' => 'counselor'], ['user_id' => $this->login->id, 'role' => 'student']]);
        $class = SchoolClass::create(['name' => 'Class 8', 'section' => 'A']);
        $this->student = Student::create(['name' => 'Student', 'student_id' => 'S100', 'user_id' => $this->login->id, 'class_id' => $class->id]);
        $this->seed(KcatDefaultSeeder::class);
    }

    private function assignment(bool $adaptive = false): KcatAssignment
    {
        $test = KcatTest::firstOrFail();
        $test->update(['status' => 'active', 'is_adaptive_enabled' => $adaptive, 'questions_per_section' => 1]);
        return KcatAssignment::create(['kcat_test_id' => $test->id, 'assigned_to_type' => 'student',
            'student_id' => $this->student->id, 'session' => '2026-2027', 'status' => 'assigned']);
    }

    public function test_counselor_pages_render_without_any_school_management_modules(): void
    {
        $this->actingAs($this->counselor);
        foreach (['dashboard', 'tests.index', 'tests.create', 'assignments.index', 'assignments.create', 'attempts.index', 'question-quality.index'] as $page) {
            $this->get(route('kcat.career-counselor.kcat.'.$page))->assertOk();
        }
        $this->get(route('kcat.career-counselor.kcat.tests.show', KcatTest::first()))->assertOk();
        $this->get(route('kcat.career-counselor.kcat.assignments.students.search', ['term' => 'Student']))->assertOk();
    }

    public function test_fixed_attempt_can_be_completed_scored_reported_and_deleted(): void
    {
        $assignment = $this->assignment();
        $this->actingAs($this->login)->post(route('kcat.student.kcat.assignments.start', $assignment))->assertRedirect();
        $attempt = KcatAttempt::firstOrFail();
        foreach ($attempt->test->questions()->with('options')->get() as $index => $question) {
            $this->get(route('kcat.student.kcat.attempts.question', [$attempt, $index]))->assertOk();
            $this->post(route('kcat.student.kcat.attempts.answer', [$attempt, $question]), [
                'selected_option_id' => $question->options->firstWhere('is_correct', true)->id,
                'next_index' => $index + 1, 'response_time_seconds' => 10,
            ])->assertRedirect();
        }
        $this->post(route('kcat.student.kcat.attempts.submit', $attempt))->assertRedirect();
        $this->assertEquals(100, $attempt->fresh()->percentage);
        $this->assertGreaterThanOrEqual(0, $attempt->fresh()->duration_seconds);
        $this->get(route('kcat.student.kcat.attempts.result', $attempt))->assertOk();
        $this->actingAs($this->counselor)->get(route('kcat.career-counselor.kcat.reports.show', $attempt))->assertOk();
        $this->get(route('kcat.career-counselor.kcat.reports.print', $attempt))->assertOk();
        $this->delete(route('kcat.career-counselor.kcat.attempts.destroy', $attempt))->assertRedirect();
        $this->assertDatabaseMissing('kcat_attempts', ['id' => $attempt->id]);
    }

    public function test_adaptive_attempt_can_complete(): void
    {
        $assignment = $this->assignment(true);
        $this->actingAs($this->login)->post(route('kcat.student.kcat.assignments.start', $assignment))->assertRedirect();
        $attempt = KcatAttempt::firstOrFail();
        for ($i = 0; $i < 12 && $attempt->fresh()->status === 'in_progress'; $i++) {
            $this->get(route('kcat.student.kcat.attempts.adaptive.next', $attempt))->assertSuccessful();
            $question = app(\Nsms\Kcat\Services\Kcat\KcatAttemptService::class)->getNextAdaptiveQuestion($attempt->fresh());
            if (! $question) { break; }
            $this->post(route('kcat.student.kcat.attempts.adaptive.answer', $attempt), [
                'selected_option_id' => $question->options()->where('is_correct', true)->first()->id,
                'response_time_seconds' => 10,
            ])->assertRedirect();
        }
        $this->assertSame('submitted', $attempt->fresh()->status);
        $this->assertGreaterThan(0, $attempt->fresh()->scores()->count());
    }

    public function test_student_cannot_manage_tests_or_open_another_students_result(): void
    {
        $this->actingAs($this->login)->get(route('kcat.career-counselor.kcat.tests.index'))->assertForbidden();
        $other = TestUser::create(['name' => 'Other', 'email' => 'other@example.test', 'password' => bcrypt('password')]);
        $candidate = Student::create(['name' => 'Other', 'student_id' => 'S200', 'user_id' => $other->id]);
        $attempt = KcatAttempt::create(['student_id' => $candidate->id, 'kcat_test_id' => KcatTest::first()->id,
            'session' => '2026-2027', 'status' => 'submitted']);
        $this->get(route('kcat.student.kcat.attempts.result', $attempt))->assertForbidden();
        $this->login->update(['name' => 'Other', 'email' => 'S200@example.test']);
        $this->get(route('kcat.student.kcat.attempts.result', $attempt))->assertForbidden();
    }

    public function test_principal_can_read_reports_but_cannot_manage_tests(): void
    {
        DB::table('kcat_access')->where('user_id', $this->counselor->id)->update(['role' => 'principal']);
        $this->actingAs($this->counselor)->get(route('kcat.principal.kcat.reports.index'))->assertOk();
        $this->get(route('kcat.principal.kcat.analytics.index'))->assertOk();
        $this->get(route('kcat.career-counselor.kcat.tests.create'))->assertForbidden();
    }
}
