@extends('layouts.app')

@section('content')
    <section class="hol-panel">
        <h1 class="text-3xl font-extrabold">My Donations</h1>
        <p class="mt-2 text-sm text-slate-700">Placeholder donor dashboard list.</p>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-600">
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Amount</th>
                        <th class="px-3 py-2">Currency</th>
                        <th class="px-3 py-2">Type</th>
                        <th class="px-3 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donations as $donation)
                        <tr class="border-t border-black/10">
                            <td class="px-3 py-2">{{ $donation->id }}</td>
                            <td class="px-3 py-2">{{ $donation->amount }}</td>
                            <td class="px-3 py-2">{{ $donation->currency }}</td>
                            <td class="px-3 py-2">{{ $donation->donation_type }}</td>
                            <td class="px-3 py-2">{{ $donation->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-slate-500">No donations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
