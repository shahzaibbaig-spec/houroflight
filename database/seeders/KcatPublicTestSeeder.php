<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Nsms\Kcat\Models\KcatTest;
use Nsms\Kcat\Services\Kcat\KcatQuestionGeneratorService;
use Nsms\Kcat\Services\Kcat\KcatTestService;

class KcatPublicTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            // Preserve existing questions and results when deployment is repeated.
            if (KcatTest::where('self_service_key', 'general')->exists()) {
                return;
            }
            $test = KcatTest::create([
                'self_service_key' => 'general', 'title' => 'KCAT Reasoning Assessment',
                'description' => 'Explore four reasoning skills with 40 questions and an instant personal report.',
                'grade_from' => 7, 'grade_to' => 12, 'duration_minutes' => 45,
                'status' => 'active', 'is_adaptive_enabled' => false,
            ]);
            $generator = app(KcatQuestionGeneratorService::class);
            $methods = ['verbal_reasoning' => 'generateVerbalQuestions',
                'quantitative_reasoning' => 'generateQuantitativeQuestions',
                'non_verbal_reasoning' => 'generateNonVerbalQuestions',
                'spatial_reasoning' => 'generateSpatialQuestions'];
            $order = 0;
            foreach (KcatTestService::DEFAULT_SECTIONS as $code => $name) {
                $section = $test->sections()->create(['code' => $code, 'name' => $name, 'sort_order' => ++$order]);
                $questionOrder = 0;
                foreach (['easy' => 4, 'medium' => 3, 'hard' => 3] as $difficulty => $count) {
                    foreach ($generator->{$methods[$code]}($count, $difficulty) as $data) {
                        $options = $data['options'];
                        unset($data['options']);
                        $question = $section->questions()->create(array_merge($data, [
                            'kcat_test_id' => $test->id, 'sort_order' => ++$questionOrder, 'is_active' => true,
                        ]));
                        foreach ($options as $index => $option) {
                            $question->options()->create(array_merge($option, ['sort_order' => $index + 1]));
                        }
                    }
                }
            }
            app(KcatTestService::class)->refreshTotals($test);
        });
    }
}
