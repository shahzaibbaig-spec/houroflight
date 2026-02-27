@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-6xl rounded-4 bg-white p-4 p-md-5 shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h2 mb-1 fw-bold">My Certificates</h1>
                <p class="mb-0 text-muted">Download certificates earned from completed courses.</p>
            </div>
            <a href="{{ route('teacher.courses.index') }}" class="btn btn-outline-primary">Back to Courses</a>
        </div>

        <div class="row g-4 mt-1">
            @forelse($certificates as $certificate)
                <div class="col-12 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 fw-bold mb-1">{{ $certificate->course?->title ?? 'Course' }}</h2>
                            <p class="small text-muted mb-3">Certificate Number: {{ $certificate->certificate_number }}</p>

                            <div class="small text-muted mb-3">
                                Issued on {{ optional($certificate->issued_at)->format('F d, Y') ?? '-' }}
                            </div>

                            @if($certificate->pdf_path)
                                <a href="{{ asset('storage/'.$certificate->pdf_path) }}" target="_blank" rel="noopener" class="btn btn-success">
                                    Download Certificate
                                </a>
                            @else
                                <button type="button" class="btn btn-outline-secondary" disabled>PDF not available</button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border mb-0">No certificates available yet. Complete a course quiz to earn one.</div>
                </div>
            @endforelse
        </div>
    </section>
@endsection
