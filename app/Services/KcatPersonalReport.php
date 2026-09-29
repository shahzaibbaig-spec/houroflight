<?php

namespace App\Services;

use Nsms\Kcat\Models\KcatAttempt;

class KcatPersonalReport
{
    public function build(KcatAttempt $attempt): array
    {
        $attempt->load(['student.user', 'test', 'scores.section', 'answers.question', 'streamRecommendations', 'latestReportNote']);
        $practice = [
            'verbal_reasoning' => 'Read short passages, explain their main idea, and practise analogies and vocabulary in context.',
            'quantitative_reasoning' => 'Practise number sequences, ratios, and multi-step word problems. Write down each step before checking your answer.',
            'non_verbal_reasoning' => 'Practise visual sequences and matrices. Look for changes in shape, position, shading, and number.',
            'spatial_reasoning' => 'Practise rotating shapes, interpreting folded paper, and matching two-dimensional views of objects.',
        ];
        $sections = $attempt->scores->map(function ($score) use ($attempt, $practice): array {
            $answers = $attempt->answers->filter(fn ($answer) => (int) $answer->question?->kcat_section_id === (int) $score->kcat_section_id);
            $answered = $answers->filter(fn ($answer) => $answer->selected_option_id !== null || filled($answer->answer_text));
            $total = $attempt->is_adaptive ? $answers->count()
                : $score->section->questions()->where('is_active', true)->whereNull('retired_at')->count();
            $correct = $answers->where('is_correct', true)->count();
            return [
                'name' => $score->section?->name ?? ucwords(str_replace('_', ' ', $score->section_code)),
                'score' => (float) $score->raw_score, 'possible' => (float) $score->total_marks,
                'percentage' => (float) $score->percentage, 'band' => ucwords(str_replace('_', ' ', $score->band ?? '')),
                'total' => $total, 'correct' => $correct, 'incorrect' => max(0, $answered->count() - $correct),
                'unanswered' => max(0, $total - $answered->count()), 'practice' => $practice[$score->section_code] ?? '',
            ];
        })->values();
        $note = $attempt->latestReportNote;

        return [
            'id' => $attempt->id, 'name' => $attempt->student?->user?->name ?? $attempt->student?->name,
            'test' => $attempt->test?->title, 'completed' => $attempt->submitted_at?->format('d M Y, H:i').' UTC',
            'mode' => $attempt->is_adaptive ? 'Adaptive' : 'Fixed',
            'duration' => $attempt->duration_seconds === null ? 'Not recorded' : gmdate('H:i:s', max(0, (int) $attempt->duration_seconds)),
            'percentage' => (float) $attempt->percentage, 'score' => (float) $attempt->total_score,
            'possible' => $sections->sum('possible'), 'band' => ucwords(str_replace('_', ' ', $attempt->band ?? '')),
            'sections' => $sections, 'strongest' => $sections->sortByDesc('percentage')->first(),
            'practice_first' => $sections->sortBy('percentage')->first(),
            'recommendations' => $attempt->streamRecommendations->map(fn ($item) => [
                'name' => $item->stream_name, 'match' => (float) $item->match_score,
                'reason' => $item->reasoning_summary,
            ]),
            'summary' => $attempt->recommendation_summary,
            // Only the explicitly shared summary belongs in a participant's report.
            'shared_summary' => $note && in_array($note->visibility, ['student', 'student_parent'], true)
                ? $note->parent_summary : null,
        ];
    }
}
