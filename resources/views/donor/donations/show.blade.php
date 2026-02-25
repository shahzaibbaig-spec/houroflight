@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <section class="container py-4" style="max-width: 980px;">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 fw-bold mb-0">Donation #{{ $donation->id }}</h1>
                    <a href="{{ route('donor.donations') }}" class="btn btn-outline-secondary btn-sm">Back</a>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <h2 class="h6 text-uppercase text-muted">Donor Info</h2>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Name:</strong> {{ $donation->donor_name }}</li>
                            <li class="list-group-item"><strong>Email:</strong> {{ $donation->donor_email }}</li>
                            <li class="list-group-item"><strong>Status:</strong> {{ $donation->status }}</li>
                            <li class="list-group-item"><strong>Type:</strong> {{ $donation->donation_type }}</li>
                        </ul>
                    </div>

                    <div class="col-12 col-md-6">
                        <h2 class="h6 text-uppercase text-muted">Payment & Stripe References</h2>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Amount:</strong> {{ number_format((float) $donation->amount, 2) }} {{ $donation->currency }}</li>
                            <li class="list-group-item"><strong>Checkout Session:</strong> {{ $donation->stripe_checkout_session_id ?? 'N/A' }}</li>
                            <li class="list-group-item"><strong>Payment Intent:</strong> {{ $donation->stripe_payment_intent_id ?? 'N/A' }}</li>
                            <li class="list-group-item"><strong>Subscription:</strong> {{ $donation->stripe_subscription_id ?? 'N/A' }}</li>
                            <li class="list-group-item">
                                <strong>Receipt:</strong>
                                @if($donation->receipt_url)
                                    <a href="{{ $donation->receipt_url }}" target="_blank" rel="noopener">View receipt</a>
                                @else
                                    N/A
                                @endif
                            </li>
                        </ul>
                    </div>

                    <div class="col-12">
                        <h2 class="h6 text-uppercase text-muted">Impact Snapshot</h2>
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                @if(is_array($donation->impact_snapshot) && count($donation->impact_snapshot))
                                    <div class="row g-2 small">
                                        <div class="col-12 col-md-6">
                                            <div class="p-3 rounded border bg-white h-100">
                                                <div class="text-muted">Amount (USD)</div>
                                                <div class="fw-semibold">
                                                    {{ number_format((float) ($donation->impact_snapshot['amount_usd'] ?? 0), 2) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="p-3 rounded border bg-white h-100">
                                                <div class="text-muted">Devices Supported</div>
                                                <div class="fw-semibold">
                                                    {{ (int) ($donation->impact_snapshot['devices_supported'] ?? 0) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="p-3 rounded border bg-white h-100">
                                                <div class="text-muted">Teacher Training Hours</div>
                                                <div class="fw-semibold">
                                                    {{ (int) ($donation->impact_snapshot['teacher_training_hours'] ?? 0) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="p-3 rounded border bg-white h-100">
                                                <div class="text-muted">Classroom Upgrade Contribution</div>
                                                <div class="fw-semibold">
                                                    {{ number_format((float) ($donation->impact_snapshot['classroom_upgrade_contribution'] ?? 0), 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No impact snapshot stored for this donation.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
