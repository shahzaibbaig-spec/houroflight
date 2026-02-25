@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-4xl">
        <div class="overflow-hidden rounded-2xl bg-[#ff4d00] p-6 text-white sm:p-8">
            <h1 class="text-4xl font-extrabold leading-none">Donate to Transform Learning</h1>
            <p class="mt-3 max-w-2xl text-sm text-white/90">
                Choose cash or hardware support. Old laptops and PCs will be repurposed for deserving schools.
            </p>
        </div>

        <div class="hol-panel mt-4">
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <p class="font-medium">Please fix the following:</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url('/donate') }}" class="mt-6 space-y-6" id="donation-form">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="donor_name" class="hol-label">Donor Name</label>
                        <input id="donor_name" name="donor_name" type="text" value="{{ old('donor_name') }}" required class="hol-input">
                    </div>

                    <div>
                        <label for="donor_email" class="hol-label">Donor Email</label>
                        <input id="donor_email" name="donor_email" type="email" value="{{ old('donor_email') }}" required class="hol-input">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="donation_type" class="hol-label">Donation Type</label>
                        <select id="donation_type" name="donation_type" required class="hol-input">
                            <option value="">Select donation type</option>
                            <option value="cash" @selected(old('donation_type') === 'cash')>Cash</option>
                            <option value="hardware" @selected(old('donation_type') === 'hardware')>Hardware</option>
                        </select>
                    </div>
                </div>

                <div id="cash-section" class="hidden rounded-2xl bg-[#1d8cf8] p-5 text-white">
                    <p class="text-sm">You selected a cash donation. Continue to payment in the next step.</p>
                    <button type="button" class="mt-3 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-[#1d8cf8]">Proceed to Payment</button>
                </div>

                <div id="hardware-section" class="hidden space-y-5 rounded-2xl border border-black/10 bg-[#efefe7] p-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="hardware_details" class="hol-label">Equipment Type</label>
                            <select id="hardware_details" name="hardware_details" class="hol-input">
                                <option value="">Select equipment</option>
                                <option value="Laptop" @selected(old('hardware_details') === 'Laptop')>Laptop</option>
                                <option value="PC" @selected(old('hardware_details') === 'PC')>PC</option>
                            </select>
                        </div>

                        <div>
                            <label for="quantity" class="hol-label">Quantity</label>
                            <input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity') }}" placeholder="e.g. 5" class="hol-input">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="hardware_condition" class="hol-label">Condition</label>
                            <input id="hardware_condition" name="hardware_condition" type="text" value="{{ old('hardware_condition') }}" placeholder="e.g. Used, Good working condition" class="hol-input">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="pickup_address" class="hol-label">Pickup Address</label>
                            <textarea id="pickup_address" name="pickup_address" rows="4" placeholder="Enter pickup location" class="hol-input">{{ old('pickup_address') }}</textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="hol-btn-secondary">Submit Donation</button>
            </form>
        </div>
    </section>

    <script>
        (function () {
            const donationType = document.getElementById('donation_type');
            const cashSection = document.getElementById('cash-section');
            const hardwareSection = document.getElementById('hardware-section');

            function toggleDonationSections() {
                const type = donationType.value;
                cashSection.classList.toggle('hidden', type !== 'cash');
                hardwareSection.classList.toggle('hidden', type !== 'hardware');
            }

            donationType.addEventListener('change', toggleDonationSections);
            toggleDonationSections();
        })();
    </script>
@endsection
