@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <section class="container py-5" style="max-width: 760px;">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5 text-center">
                <h1 class="display-6 fw-bold">Thank You For Your Donation</h1>
                <p class="mt-3 mb-2 text-secondary">
                    Receipt will be emailed shortly.
                </p>
                <p class="small text-muted mb-4">
                    Payment confirmation on this page is informational only. Webhook verification is the source of truth for final payment status.
                </p>
                <a href="{{ route('donate.form') }}" class="btn btn-primary px-4">Make Another Donation</a>
            </div>
        </div>
    </section>
@endsection
