@extends('layouts.app')

@section('content')
    <section class="container py-4" style="max-width: 980px;">
        @php
            $volunteerRegisterUrl = \Illuminate\Support\Facades\Route::has('volunteer.register')
                ? route('volunteer.register')
                : route('register');
        @endphp

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
                <h1 class="h2 fw-bold mb-2">Choose How You Want to Donate</h1>
                <p class="text-secondary mb-4">Use one donation page for money, hardware, or your time as a volunteer teacher.</p>

                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <button type="button" class="btn btn-outline-primary w-100 donate-mode-btn active" data-mode="money">Donate Money</button>
                    </div>
                    <div class="col-12 col-md-4">
                        <button type="button" class="btn btn-outline-primary w-100 donate-mode-btn" data-mode="hardware">Donate Hardware</button>
                    </div>
                    <div class="col-12 col-md-4">
                        <button type="button" class="btn btn-outline-primary w-100 donate-mode-btn" data-mode="time">Donate Time</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-7" id="moneyPanel">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 fw-bold mb-2">Cash Donation</h2>
                        <p class="text-secondary mb-4">Pledge your cash donation and upload payment proof for verification.</p>

                        <div class="alert alert-primary border-primary-subtle mb-4">
                            <h3 class="h6 fw-bold mb-2">Bank Transfer Details (for pledged cash donations)</h3>
                            <div class="small mb-1"><strong>Account Title:</strong> MUHAMMAD SHAHZAIB BAIG</div>
                            <div class="small mb-1"><strong>Account Number:</strong> 0522679471005466</div>
                            <div class="small mb-1"><strong>Bank:</strong> MCB MIRPUR AJK BRANCH</div>
                            <div class="small mb-1"><strong>Branch Code:</strong> 1341</div>
                            <div class="small mb-0"><strong>IBAN:</strong> PK08 MUCB 0522 6794 7100 5466</div>
                        </div>

                        <div id="validationErrors" class="alert alert-danger d-none" role="alert"></div>

                        <form id="donationForm" class="row g-3" action="{{ route('donate.checkout') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-12 col-md-6">
                                <label for="donor_name" class="form-label">Donor Name</label>
                                <input type="text" class="form-control" id="donor_name" name="donor_name" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="donor_email" class="form-label">Donor Email</label>
                                <input type="email" class="form-control" id="donor_email" name="donor_email" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-select" id="currency" name="currency" required>
                                    <option value="USD">USD</option>
                                    <option value="PKR">PKR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="EUR">EUR</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="donation_type" class="form-label">Donation Type</label>
                                <select class="form-select" id="donation_type" name="donation_type" required>
                                    <option value="one_time">one_time</option>
                                    <option value="monthly">monthly</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="proof_type" class="form-label">Proof Type</label>
                                <select class="form-select" id="proof_type" name="proof_type" required>
                                    <option value="">Select proof type</option>
                                    <option value="image">Image</option>
                                    <option value="pdf">PDF</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="payment_screenshot" class="form-label">Payment Proof File (Required)</label>
                                <input type="file" class="form-control" id="payment_screenshot" name="payment_screenshot" accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" required>
                                <div class="form-text">Upload proof according to selected type. Submission is not allowed without a proof file.</div>
                            </div>

                            @guest
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="create_account" name="create_account">
                                        <label class="form-check-label" for="create_account">
                                            Create an account so I can view my donor dashboard
                                        </label>
                                    </div>
                                </div>
                            @endguest

                            <div class="col-12 mt-2">
                                <button id="donateBtn" type="submit" class="btn btn-primary px-4 py-2">
                                    Donate Now
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5" id="moneyImpactPanel">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold">Estimated Impact</h2>
                        <p class="text-secondary small mb-4">Updated live based on amount and currency.</p>

                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small">Devices Supported</div>
                                <div id="impact_devices" class="fs-4 fw-bold">0</div>
                            </div>
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small">Teacher Training Hours</div>
                                <div id="impact_training" class="fs-4 fw-bold">0</div>
                            </div>
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small">Classroom Upgrade Contribution (%)</div>
                                <div id="impact_classroom" class="fs-4 fw-bold">0.0%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="hardwarePanel" class="d-none">
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h4 fw-bold mb-2">Hardware Donation</h2>
                    <p class="text-secondary mb-4">Donate old laptops or PCs. We refurbish and repurpose them for deserving schools.</p>

                    <form method="POST" action="{{ route('donate.hardware') }}" class="row g-3">
                        @csrf
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="hd_donor_name">Donor Name</label>
                            <input id="hd_donor_name" name="donor_name" type="text" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="hd_donor_email">Donor Email</label>
                            <input id="hd_donor_email" name="donor_email" type="email" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="hd_hardware_type">Device Type</label>
                            <select id="hd_hardware_type" name="hardware_type" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Laptop">Laptop</option>
                                <option value="PC">PC</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="hd_quantity">Quantity</label>
                            <input id="hd_quantity" name="quantity" type="number" min="1" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="hd_condition">Condition</label>
                            <input id="hd_condition" name="hardware_condition" type="text" class="form-control" placeholder="Used / Good">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="hd_pickup_address">Pickup Address</label>
                            <textarea id="hd_pickup_address" name="pickup_address" rows="3" class="form-control" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="hd_message">Message (optional)</label>
                            <textarea id="hd_message" name="message" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit Hardware Donation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="timePanel" class="d-none">
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h4 fw-bold mb-2">Donate Time (Volunteer Teacher)</h2>
                    <p class="text-secondary mb-4">Share your teaching skills with underserved schools by registering as a volunteer teacher.</p>
                    <a href="{{ $volunteerRegisterUrl }}" class="btn btn-primary">Continue to Volunteer Registration</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        (function () {
            const modeButtons = document.querySelectorAll('.donate-mode-btn');
            const moneyPanel = document.getElementById('moneyPanel');
            const moneyImpactPanel = document.getElementById('moneyImpactPanel');
            const hardwarePanel = document.getElementById('hardwarePanel');
            const timePanel = document.getElementById('timePanel');

            const form = document.getElementById('donationForm');
            const amountInput = document.getElementById('amount');
            const currencyInput = document.getElementById('currency');
            const proofTypeInput = document.getElementById('proof_type');
            const proofFileInput = document.getElementById('payment_screenshot');
            const donateBtn = document.getElementById('donateBtn');
            const errorsBox = document.getElementById('validationErrors');

            const impactDevices = document.getElementById('impact_devices');
            const impactTraining = document.getElementById('impact_training');
            const impactClassroom = document.getElementById('impact_classroom');

            function setMode(mode) {
                modeButtons.forEach((btn) => {
                    const active = btn.dataset.mode === mode;
                    btn.classList.toggle('active', active);
                    btn.classList.toggle('btn-primary', active);
                    btn.classList.toggle('btn-outline-primary', !active);
                });

                const showMoney = mode === 'money';
                moneyPanel.classList.toggle('d-none', !showMoney);
                moneyImpactPanel.classList.toggle('d-none', !showMoney);
                hardwarePanel.classList.toggle('d-none', mode !== 'hardware');
                timePanel.classList.toggle('d-none', mode !== 'time');

                if (mode === 'time') {
                    const volunteerUrl = @json($volunteerRegisterUrl);
                    setTimeout(() => { window.location.href = volunteerUrl; }, 300);
                }
            }

            modeButtons.forEach((btn) => {
                btn.addEventListener('click', () => setMode(btn.dataset.mode));
            });

            function syncProofInputByType() {
                if (!proofTypeInput || !proofFileInput) {
                    return;
                }

                if (proofTypeInput.value === 'pdf') {
                    proofFileInput.setAttribute('accept', '.pdf,application/pdf');
                } else if (proofTypeInput.value === 'image') {
                    proofFileInput.setAttribute('accept', '.jpg,.jpeg,.png,.webp,image/*');
                } else {
                    proofFileInput.setAttribute('accept', '.pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*');
                }

                // Reset previous selection when type changes to avoid invalid uploads.
                proofFileInput.value = '';
            }

            function showErrors(messages) {
                if (!messages.length) {
                    errorsBox.classList.add('d-none');
                    errorsBox.innerHTML = '';
                    return;
                }

                errorsBox.innerHTML = '<ul class="mb-0">' + messages.map((m) => `<li>${m}</li>`).join('') + '</ul>';
                errorsBox.classList.remove('d-none');
            }

            async function refreshImpact() {
                const amount = amountInput.value || 0;
                const currency = currencyInput.value || 'USD';

                try {
                    const res = await fetch(`{{ route('donate.impact') }}?amount=${encodeURIComponent(amount)}&currency=${encodeURIComponent(currency)}`);
                    if (!res.ok) {
                        return;
                    }

                    const data = await res.json();
                    impactDevices.textContent = data.devices_supported ?? 0;
                    impactTraining.textContent = data.teacher_training_hours ?? 0;
                    const classroomPct = data.classroom_upgrade_contribution ?? '0.0';
                    impactClassroom.textContent = `${classroomPct}%`;
                } catch (_) {
                    // Silent fail for non-critical live estimate.
                }
            }

            form.setAttribute('action', '{{ route('donate.pledge') }}');
            form.setAttribute('method', 'POST');
            if (proofTypeInput) {
                proofTypeInput.addEventListener('change', syncProofInputByType);
                syncProofInputByType();
            }

            amountInput.addEventListener('input', refreshImpact);
            currencyInput.addEventListener('change', refreshImpact);
            setMode('money');
            refreshImpact();
        })();
    </script>
@endsection
