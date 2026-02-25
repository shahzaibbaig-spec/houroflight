@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <section class="container py-4" style="max-width: 1100px;">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="h3 fw-bold mb-0">My Donations</h1>
                    <a href="{{ route('donate.form') }}" class="btn btn-primary btn-sm">New Donation</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Currency</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Receipt</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($donations as $donation)
                                <tr>
                                    <td>{{ $donation->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ number_format((float) $donation->amount, 2) }}</td>
                                    <td>{{ $donation->currency }}</td>
                                    <td>{{ $donation->donation_type }}</td>
                                    <td>
                                        <span class="badge text-bg-{{ $donation->status === 'succeeded' ? 'success' : ($donation->status === 'pending' ? 'warning' : ($donation->status === 'failed' ? 'danger' : 'secondary')) }}">
                                            {{ $donation->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($donation->receipt_url)
                                            <a href="{{ $donation->receipt_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">Receipt</a>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('donor.donations.show', $donation) }}" class="btn btn-outline-dark btn-sm">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No donations found yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
