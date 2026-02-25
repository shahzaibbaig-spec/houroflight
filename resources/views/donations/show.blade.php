@extends('layouts.app')

@section('content')
    <section class="hol-panel mx-auto max-w-3xl">
        <h1 class="text-3xl font-extrabold">Donation #{{ $donation->id }}</h1>
        <div class="mt-4 space-y-2 text-sm text-slate-700">
            <p><span class="font-semibold">Donor:</span> {{ $donation->donor_name }} ({{ $donation->donor_email }})</p>
            <p><span class="font-semibold">Amount:</span> {{ $donation->amount }} {{ $donation->currency }}</p>
            <p><span class="font-semibold">Type:</span> {{ $donation->donation_type }}</p>
            <p><span class="font-semibold">Status:</span> {{ $donation->status }}</p>
        </div>
    </section>
@endsection
