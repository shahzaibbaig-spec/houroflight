<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Nsms\Kcat\Models\KcatAssignment;
use Nsms\Kcat\Models\KcatAttempt;
use Nsms\Kcat\Models\KcatTest;
use Nsms\Kcat\Models\SchoolClass;
use Nsms\Kcat\Models\Student;
use Nsms\Kcat\Seeders\KcatDefaultSeeder;
use Tests\TestCase;

class KcatIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function grantStudent(User $user, string $id = 'STU001'): Student
    {
        DB::table('kcat_access')->insert(['user_id' => $user->id, 'role' => 'student']);
        $class = SchoolClass::firstOrCreate(['name' => 'Class 8', 'section' => 'A']);

        return Student::create(['user_id' => $user->id, 'student_id' => $id, 'name' => $user->name, 'class_id' => $class->id]);
    }

    public function test_portal_is_public_and_login_returns_to_kcat(): void
    {
        $this->get(route('kcat.index'))->assertOk()->assertSee('KORT Cognitive Assessment Test');
        $this->get(route('kcat.login'))->assertRedirect(route('login'));
        $user = User::factory()->create(['role' => User::ROLE_DONOR]);
        $this->post(route('login.perform'), ['login' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('kcat.login'));
        $this->get(route('kcat.login'))->assertRedirect(route('kcat.index'));
        $this->get(route('kcat.index'))->assertOk()->assertSee('My assigned tests');
    }

    public function test_existing_admin_can_manage_kcat_without_a_separate_grant(): void
    {
        $this->actingAs($this->admin());
        $this->get(route('kcat.index'))->assertOk()->assertSee('Manage assessments');
        $this->get(route('kcat.career-counselor.kcat.dashboard'))->assertOk()->assertSee('Student &amp; staff access', false);
        $this->get(route('admin.kcat.access.index'))->assertOk();
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Manage KCAT');
        $this->get(route('kcat.career-counselor.kcat.tests.create'))->assertOk();
    }

    public function test_admin_can_link_student_without_changing_website_role_or_password(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_DONOR]);
        $password = $user->password;
        $this->actingAs($this->admin())->post(route('admin.kcat.access.store'), [
            'email' => $user->email, 'role' => 'student', 'student_id' => 'STU007', 'grade' => 8, 'section' => 'B',
        ])->assertRedirect(route('admin.kcat.access.index'))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kcat_access', ['user_id' => $user->id, 'role' => 'student']);
        $this->assertDatabaseHas('kcat_students', ['user_id' => $user->id, 'student_id' => 'STU007', 'status' => 'active']);
        $this->assertSame(User::ROLE_DONOR, $user->fresh()->role);
        $this->assertSame($password, $user->fresh()->password);
        $this->actingAs($user)->get(route('kcat.index'))->assertOk();
        $this->get(route('kcat.student.kcat.assignments.index'))->assertOk();
    }

    public function test_access_management_is_admin_only(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_DONOR]);
        $this->actingAs($user)->get(route('admin.kcat.access.index'))->assertForbidden();
        $this->post(route('admin.kcat.access.store'), ['email' => $user->email, 'role' => 'counselor'])->assertForbidden();
        $this->delete(route('admin.kcat.access.destroy', $user))->assertForbidden();
        $this->get(route('kcat.career-counselor.kcat.tests.index'))->assertForbidden();
    }

    public function test_invalid_or_duplicate_student_details_do_not_grant_access(): void
    {
        $existing = User::factory()->create();
        $this->grantStudent($existing);
        $user = User::factory()->create();
        $this->actingAs($this->admin())->post(route('admin.kcat.access.store'), [
            'email' => $user->email, 'role' => 'student', 'student_id' => 'STU001', 'grade' => 8,
        ])->assertSessionHasErrors('student_id');
        $this->post(route('admin.kcat.access.store'), [
            'email' => $user->email, 'role' => 'student', 'student_id' => 'STU002', 'grade' => 6,
        ])->assertSessionHasErrors('grade');
        $this->assertDatabaseMissing('kcat_access', ['user_id' => $user->id]);
    }

    public function test_removing_assigned_role_preserves_public_access_and_results(): void
    {
        $user = User::factory()->create();
        $student = $this->grantStudent($user);
        $this->seed(KcatDefaultSeeder::class);
        $attempt = KcatAttempt::create(['kcat_test_id' => KcatTest::first()->id,
            'student_id' => $student->id, 'session' => '2026-2027', 'status' => 'submitted']);
        $this->actingAs($this->admin())->delete(route('admin.kcat.access.destroy', $user))->assertRedirect();
        $this->assertDatabaseHas('kcat_attempts', ['id' => $attempt->id]);
        $this->assertDatabaseHas('kcat_students', ['id' => $student->id, 'status' => 'inactive']);
        $this->actingAs($user)->get(route('kcat.student.kcat.assignments.index'))->assertOk();
    }

    public function test_student_without_class_cannot_see_other_students_assignments(): void
    {
        $user = User::factory()->create();
        $student = $this->grantStudent($user);
        $student->update(['class_id' => null]);
        $other = $this->grantStudent(User::factory()->create(), 'STU002');
        $this->seed(KcatDefaultSeeder::class);
        $test = KcatTest::first();
        $test->update(['title' => 'Private assignment for another student']);
        KcatAssignment::create(['kcat_test_id' => $test->id, 'assigned_to_type' => 'student',
            'student_id' => $other->id, 'session' => '2026-2027', 'status' => 'assigned']);
        $this->actingAs($user)->get(route('kcat.student.kcat.assignments.index'))
            ->assertOk()->assertDontSee('Private assignment for another student');
    }

    public function test_test_creation_requires_valid_question_generation_parameters(): void
    {
        $this->seed(KcatDefaultSeeder::class);
        $this->actingAs($this->admin())->post(route('kcat.career-counselor.kcat.tests.store'), [
            'title' => 'New assessment',
        ])->assertSessionHasErrors(['question_count', 'difficulty_level']);
        $this->post(route('kcat.career-counselor.kcat.tests.store'), [
            'title' => 'New assessment', 'question_count' => 5, 'difficulty_level' => 'easy',
        ])->assertSessionHasErrors('question_count');
        $this->post(route('kcat.career-counselor.kcat.tests.store'), [
            'title' => 'New assessment', 'question_count' => 4, 'difficulty_level' => 'easy',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame(4, KcatTest::where('title', 'New assessment')->firstOrFail()->questions()->count());
    }

    public function test_class_assignments_remain_available_after_another_student_finishes(): void
    {
        $first = User::factory()->create();
        $firstStudent = $this->grantStudent($first);
        $second = User::factory()->create();
        $this->grantStudent($second, 'STU002');
        $this->seed(KcatDefaultSeeder::class);
        $test = KcatTest::first();
        $test->update(['status' => 'active']);
        $assignment = KcatAssignment::create(['kcat_test_id' => $test->id, 'assigned_to_type' => 'class',
            'class_id' => $firstStudent->class_id, 'session' => '2026-2027', 'status' => 'completed']);
        $attempt = KcatAttempt::create(['kcat_test_id' => $test->id, 'kcat_assignment_id' => $assignment->id,
            'student_id' => $firstStudent->id, 'session' => '2026-2027', 'status' => 'submitted']);
        $this->actingAs($first)->get(route('kcat.student.kcat.assignments.index'))->assertOk()->assertSee('View result');
        $this->post(route('kcat.student.kcat.assignments.start', $assignment))
            ->assertRedirect(route('kcat.student.kcat.attempts.result', $attempt));
        $this->assertDatabaseCount('kcat_attempts', 1);
        $this->actingAs($second)->get(route('kcat.student.kcat.assignments.index'))->assertOk()->assertSee('Start');
        $this->post(route('kcat.student.kcat.assignments.start', $assignment))->assertRedirect();
        $this->assertDatabaseCount('kcat_attempts', 2);
        $this->get(route('kcat.student.kcat.attempts.result', $attempt))->assertForbidden();
    }

    public function test_archived_tests_cannot_be_started(): void
    {
        $user = User::factory()->create();
        $student = $this->grantStudent($user);
        $this->seed(KcatDefaultSeeder::class);
        $test = KcatTest::first();
        $test->update(['status' => 'archived']);
        $assignment = KcatAssignment::create(['kcat_test_id' => $test->id, 'assigned_to_type' => 'student',
            'student_id' => $student->id, 'session' => '2026-2027', 'status' => 'assigned']);
        $this->actingAs($user)->post(route('kcat.student.kcat.assignments.start', $assignment))->assertForbidden();
        $this->assertDatabaseCount('kcat_attempts', 0);
    }
}
