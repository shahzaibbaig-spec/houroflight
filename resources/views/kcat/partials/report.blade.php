<article class="kcat-report">
    <header class="report-header">
        <p class="muted">HOUR OF LIGHT · PERSONAL ASSESSMENT REPORT</p>
        <h1>KCAT Detailed Report</h1>
        <p><strong>{{ $report['name'] }}</strong> · Report #{{ $report['id'] }}</p>
        <p>{{ $report['test'] }}</p>
        <p class="muted">Completed: {{ $report['completed'] }} · {{ $report['mode'] }} assessment · Time: {{ $report['duration'] }}</p>
    </header>
    <section class="report-score">
        <p class="score-number">{{ number_format($report['percentage'], 1) }}%</p>
        <p><strong>{{ $report['score'] }} / {{ $report['possible'] }} marks</strong> · {{ $report['band'] }}</p>
        <p>Your overall score combines the marks earned across all four reasoning areas.</p>
    </section>
    <h2>Section breakdown</h2>
    <table>
        <thead><tr><th>Reasoning area</th><th>Marks</th><th>Score</th><th>Correct</th><th>Incorrect</th><th>Unanswered</th></tr></thead>
        <tbody>@foreach($report['sections'] as $section)
            <tr><td>{{ $section['name'] }}</td><td>{{ $section['score'] }}/{{ $section['possible'] }}</td><td>{{ number_format($section['percentage'], 1) }}%</td><td>{{ $section['correct'] }}</td><td>{{ $section['incorrect'] }}</td><td>{{ $section['unanswered'] }}</td></tr>
        @endforeach</tbody>
    </table>
    <h2>Your strengths and next steps</h2>
    @if($report['strongest'])
        <p>Your highest section score was <strong>{{ $report['strongest']['name'] }}</strong> at {{ number_format($report['strongest']['percentage'], 1) }}%.</p>
    @endif
    @if($report['practice_first'])
        <p>A useful starting point for practice is <strong>{{ $report['practice_first']['name'] }}</strong>, your lowest-scoring section at {{ number_format($report['practice_first']['percentage'], 1) }}%. Tied sections are equally good starting points.</p>
    @endif
    @foreach($report['sections'] as $section)
        <section class="report-card">
            <h3>{{ $section['name'] }} · {{ $section['band'] }}</h3>
            <p>{{ $section['practice'] }}</p>
        </section>
    @endforeach
    <h2>Subject stream suggestions</h2>
    <p class="muted">These matches use the balance of your section scores. Explore them alongside your interests and schoolwork when choosing subjects.</p>
    @forelse($report['recommendations'] as $recommendation)
        <section class="report-card">
            <h3>{{ $loop->iteration }}. {{ $recommendation['name'] }} · {{ number_format($recommendation['match'], 1) }}% match</h3>
            <p>{{ $recommendation['reason'] }}</p>
        </section>
    @empty
        <p>No stream suggestions were recorded for this attempt.</p>
    @endforelse
    @if($report['summary'])<p>{{ $report['summary'] }}</p>@endif
    @if($report['shared_summary'])
        <h2>Shared counselor summary</h2><p class="pre-line">{{ $report['shared_summary'] }}</p>
    @endif
    <h2>How to read your scores</h2>
    <p>Section scores show the percentage of available marks earned. The test's bands are: Exceptional (90–100%), Strong (75–89%), Above Average (60–74%), Average (45–59%), and Needs Support (below 45%). These labels describe this test's scoring scale.</p>
    <p class="muted">This report is generated automatically after submission. You can download it again from your KCAT page.</p>
</article>
