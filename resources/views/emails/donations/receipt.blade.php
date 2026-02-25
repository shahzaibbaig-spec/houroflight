<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Donation Receipt</title>
</head>
<body class="bg-light py-4">
    <div class="container" style="max-width: 640px;">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <h2 class="fw-bold mb-3">Thank you for your donation to Hour of Light</h2>
                <p class="text-secondary mb-4">Your generosity helps us expand access to quality education.</p>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-4">
                        <tbody>
                            <tr>
                                <th scope="row" class="w-50">Donor Name</th>
                                <td>{{ $donation->donor_name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Amount</th>
                                <td>{{ number_format((float) $donation->amount, 2) }} {{ $donation->currency }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Donation Type</th>
                                <td>{{ $donation->donation_type }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Date</th>
                                <td>{{ $donation->created_at?->format('F j, Y g:i A') ?? now()->format('F j, Y g:i A') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Reference</th>
                                <td>#{{ $donation->id }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($donation->receipt_url)
                    <a href="{{ $donation->receipt_url }}" class="btn btn-primary">View Stripe receipt</a>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
