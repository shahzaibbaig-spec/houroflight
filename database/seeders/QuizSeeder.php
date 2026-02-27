<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $course = Course::where('slug', 'continuous-learning')->first();

        if (! $course) {
            return;
        }

        $questions = [
            [
                'question' => 'Why is continuous learning essential for teachers in modern classrooms?',
                'marks' => 1,
                'options' => [
                    ['option_text' => 'It reduces the need for lesson planning.', 'is_correct' => false],
                    ['option_text' => 'It helps teachers adapt and improve student outcomes over time.', 'is_correct' => true],
                    ['option_text' => 'It guarantees perfect exam scores immediately.', 'is_correct' => false],
                    ['option_text' => 'It replaces all classroom management responsibilities.', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Which statement best reflects a growth mindset in teaching practice?',
                'marks' => 1,
                'options' => [
                    ['option_text' => 'My teaching ability is fixed and cannot improve much.', 'is_correct' => false],
                    ['option_text' => 'Student failure means I should not try new methods.', 'is_correct' => false],
                    ['option_text' => 'I can improve my teaching through effort, reflection, and feedback.', 'is_correct' => true],
                    ['option_text' => 'Only experienced teachers can change instructional outcomes.', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is a core feature of a strong learning culture in a school?',
                'marks' => 1,
                'options' => [
                    ['option_text' => 'Avoiding classroom observation to prevent discomfort.', 'is_correct' => false],
                    ['option_text' => 'Treating mistakes as data for improvement.', 'is_correct' => true],
                    ['option_text' => 'Limiting collaboration to annual meetings only.', 'is_correct' => false],
                    ['option_text' => 'Focusing only on final results without reflection.', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Which behavior is most aligned with a master learner teacher?',
                'marks' => 1,
                'options' => [
                    ['option_text' => 'Collecting many resources without applying them.', 'is_correct' => false],
                    ['option_text' => 'Practicing one instructional skill deliberately and measuring impact.', 'is_correct' => true],
                    ['option_text' => 'Changing multiple strategies daily without tracking results.', 'is_correct' => false],
                    ['option_text' => 'Avoiding peer feedback to maintain personal style.', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'How does creativity improve teaching and learning quality?',
                'marks' => 1,
                'options' => [
                    ['option_text' => 'By making lessons entertaining without objectives.', 'is_correct' => false],
                    ['option_text' => 'By replacing curriculum goals with random activities.', 'is_correct' => false],
                    ['option_text' => 'By designing engaging methods that still align with clear outcomes.', 'is_correct' => true],
                    ['option_text' => 'By removing all structure from classroom tasks.', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the first step in effective behaviour modification for teachers?',
                'marks' => 1,
                'options' => [
                    ['option_text' => 'Identify one high-impact behavior to change.', 'is_correct' => true],
                    ['option_text' => 'Change all weak habits at once.', 'is_correct' => false],
                    ['option_text' => 'Wait for annual appraisals before making adjustments.', 'is_correct' => false],
                    ['option_text' => 'Ignore classroom evidence to reduce stress.', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Which response shows receptiveness to professional feedback?',
                'marks' => 1,
                'options' => [
                    ['option_text' => 'Rejecting all feedback that challenges current routines.', 'is_correct' => false],
                    ['option_text' => 'Treating feedback as personal criticism and avoiding it.', 'is_correct' => false],
                    ['option_text' => 'Asking clarifying questions and testing suggested improvements.', 'is_correct' => true],
                    ['option_text' => 'Accepting feedback only from students, never colleagues.', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Which weekly routine best supports continuous learning?',
                'marks' => 1,
                'options' => [
                    ['option_text' => 'No routine, learning only when problems escalate.', 'is_correct' => false],
                    ['option_text' => 'A cycle of learning, classroom application, and reflection.', 'is_correct' => true],
                    ['option_text' => 'Reading theory only without classroom experimentation.', 'is_correct' => false],
                    ['option_text' => 'Switching goals every day without evidence tracking.', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What best demonstrates collaborative learning culture among teachers?',
                'marks' => 1,
                'options' => [
                    ['option_text' => 'Private lesson planning with no sharing of outcomes.', 'is_correct' => false],
                    ['option_text' => 'Peer observation and joint review of student work.', 'is_correct' => true],
                    ['option_text' => 'Avoiding team meetings to save preparation time.', 'is_correct' => false],
                    ['option_text' => 'Comparing teachers only by test rankings.', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the main purpose of measuring professional growth with evidence?',
                'marks' => 1,
                'options' => [
                    ['option_text' => 'To confirm whether changed practices improve student learning.', 'is_correct' => true],
                    ['option_text' => 'To eliminate the need for teacher reflection.', 'is_correct' => false],
                    ['option_text' => 'To focus only on attendance and punctuality.', 'is_correct' => false],
                    ['option_text' => 'To replace classroom assessments completely.', 'is_correct' => false],
                ],
            ],
        ];

        DB::transaction(function () use ($course, $questions): void {
            $quiz = Quiz::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => 'Continuous Learning Final Assessment',
                ],
                [
                    'total_marks' => 10,
                ]
            );

            $quiz->questions()->delete();

            foreach ($questions as $questionData) {
                $options = $questionData['options'];
                unset($questionData['options']);

                $question = $quiz->questions()->create($questionData);
                $question->options()->createMany($options);
            }
        });
    }
}
