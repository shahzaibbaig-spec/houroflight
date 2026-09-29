@extends('layouts.app')

@section('content')
    @php($report = app(\App\Services\KcatPersonalReport::class)->build($attempt))
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('kcat.index') }}" class="hol-cta-secondary">Back to KCAT</a>
        <a href="{{ route('kcat.report.download', $attempt) }}" class="hol-cta-primary">Download detailed report (PDF)</a>
    </div>
    <section class="hol-panel overflow-x-auto">
        @include('kcat.partials.report-styles')
        @include('kcat.partials.report')
    </section>
@endsection
