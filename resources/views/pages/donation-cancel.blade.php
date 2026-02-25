@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <section class="container py-5" style="max-width: 760px;">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5 text-center">
                <h1 class="display-6 fw-bold">Donation Canceled</h1>
                <p class="mt-3 text-secondary">
                    Your donation was canceled. No charge was completed.
                </p>
                <a href="{{ route('donate.form') }}" class="btn btn-primary px-4 mt-2">Try Again</a>
            </div>
        </div>
    </section>
@endsection
