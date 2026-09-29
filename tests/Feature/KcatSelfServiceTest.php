<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\KcatPersonalReport;
use Database\Seeders\KcatPublicTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nsms\Kcat\Models\KcatAttempt;
use Nsms\Kcat\Models\KcatTest;
use Nsms\Kcat\Models\Student;
use Tests\TestCase;

class KcatSelfServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(KcatPublicTestSeeder::class);
    }

    private function publicTest(): KcatTest
    {
        return KcatTest::where('self_service_key', 'general')->firstOrFail();
    }

    public function test_signup_to_complete_test_and_download_pdf_without_any_grant(): void
    {
        $this->post(route('register.perform'), [
            'name' => 'New Participant', 'email' => 'participant@example.test',
            'password' => 'strong-test-password', 'password_confirmation' => 'strong-test-password',
        ])->assertRedirect(route('kcat.index'))->assertSessionHasNoErrors();
        $user = User::where('email', 'participant@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->approved_at);
        $this->assertDatabaseCount('kcat_access', 0);
        $this->get(route('kcat.index'))->assertOk()->assertSee('Start KCAT');
        $this->post(route('kcat.start', $this->publicTest()))->assertRedirect();
        $attempt = KcatAttempt::firstOrFail();
        $this->get(route('kcat.student.kcat.attempts.question', [$attempt, 0]))->assertOk();
        $this->get(route('kcat.report.download', $attempt))->assertForbidden();
        $this->get(route('kcat.student.kcat.attempts.result', $attempt))->assertForbidden();
        foreach ($attempt->test->questions()->with('options')->get() as $index => $question) {
            $this->post(route('kcat.student.kcat.attempts.answer', [$attempt, $question]), [
                'selected_option_id' => $question->options->firstWhere('is_correct', true)->id,
                'next_index' => $index + 1, 'response_time_seconds' => 10,
            ])->assertRedirect()->assertSessionHasNoErrors();
        }
        $this->post(route('kcat.student.kcat.attempts.submit', $attempt))->assertRedirect();
        $this->assertEquals(100, $attempt->fresh()->percentage);
        $this->get(route('kcat.student.kcat.attempts.result', $attempt))->assertOk()
            ->assertSee('KCAT Detailed Report')->assertSee('Section breakdown')->assertSee('Subject stream suggestions');
        $download = $this->get(route('kcat.report.download', $attempt));
        $download->assertOk()->assertHeader('content-type', 'application/pdf')
            ->assertDownload('KCAT-Report-'.$attempt->id.'.pdf');
        $this->assertStringStartsWith('%PDF-', $download->getContent());
        $this->get(route('kcat.index'))->assertOk()->assertSee('Download PDF');
        $this->post(route('kcat.start', $this->publicTest()))
            ->assertRedirect(route('kcat.student.kcat.attempts.result', $attempt));
        $this->assertDatabaseCount('kcat_attempts', 1);
        $this->assertDatabaseCount('kcat_access', 0);
    }

    public function test_existing_accounts_get_access_and_repeated_start_resumes(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_DONOR]);
        $this->actingAs($user)->post(route('kcat.start', $this->publicTest()))->assertRedirect();
        $this->post(route('kcat.start', $this->publicTest()))->assertRedirect();
        $this->assertDatabaseCount('kcat_attempts', 1);
        $this->assertDatabaseCount('kcat_assignments', 1);
        $this->assertDatabaseCount('kcat_students', 1);
        $this->assertDatabaseCount('kcat_access', 0);
        $this->get(route('kcat.career-counselor.kcat.tests.index'))->assertForbidden();
        $this->get(route('admin.kcat.access.index'))->assertForbidden();
    }

    public function test_guests_and_other_accounts_cannot_download_or_view_private_reports(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('kcat.start', $this->publicTest()));
        $attempt = KcatAttempt::firstOrFail();
        $this->post(route('kcat.student.kcat.attempts.submit', $attempt))->assertRedirect();
        $this->actingAs(User::factory()->create())->get(route('kcat.report.download', $attempt))->assertForbidden();
        $this->get(route('kcat.student.kcat.attempts.result', $attempt))->assertForbidden();
        $this->get(route('kcat.index'))->assertDontSee('Download PDF');
        $this->post(route('logout'));
        $this->get(route('kcat.report.download', $attempt))->assertRedirect(route('login'));
        $this->post(route('kcat.start', $this->publicTest()))->assertRedirect(route('login'));
    }

    public function test_drafts_and_private_tests_cannot_be_self_assigned(): void
    {
        $this->actingAs(User::factory()->create());
        $test = $this->publicTest();
        $test->update(['self_service_key' => null]);
        $this->post(route('kcat.start', $test))->assertNotFound();
        $test->update(['self_service_key' => 'general', 'status' => 'draft']);
        $this->post(route('kcat.start', $test))->assertNotFound();
        $this->get(route('kcat.index'))->assertDontSee('KCAT Reasoning Assessment');
        $this->assertDatabaseCount('kcat_attempts', 0);
    }

    public function test_private_counselor_notes_are_excluded_from_report_data_and_html(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('kcat.start', $this->publicTest()));
        $attempt = KcatAttempt::firstOrFail();
        $this->post(route('kcat.student.kcat.attempts.submit', $attempt));
        $note = $attempt->reportNotes()->create([
            'counselor_id' => $user->id, 'visibility' => 'private',
            'private_notes' => 'SECRET_PRIVATE_NOTE', 'parent_summary' => 'SECRET_PARENT_SUMMARY',
            'counselor_recommendation' => 'SECRET_RECOMMENDATION',
        ]);
        $report = app(KcatPersonalReport::class)->build($attempt->fresh());
        $this->assertStringNotContainsString('SECRET', json_encode($report));
        $this->get(route('kcat.student.kcat.attempts.result', $attempt))->assertOk()->assertDontSee('SECRET');
        $note->update(['visibility' => 'student', 'parent_summary' => 'Shared encouragement']);
        $this->get(route('kcat.student.kcat.attempts.result', $attempt))->assertOk()
            ->assertSee('Shared encouragement')->assertDontSee('SECRET');
        $this->assertStringNotContainsString('SECRET', json_encode(app(KcatPersonalReport::class)->build($attempt->fresh())));
    }

    public function test_public_test_seeding_is_repeatable_and_preserves_questions(): void
    {
        $ids = $this->publicTest()->questions()->pluck('id')->all();
        $this->seed(KcatPublicTestSeeder::class);
        $this->assertDatabaseCount('kcat_tests', 1);
        $this->assertCount(40, $ids);
        $this->assertSame($ids, $this->publicTest()->questions()->pluck('id')->all());
        $this->assertDatabaseCount('kcat_question_options', 160);
    }
}
