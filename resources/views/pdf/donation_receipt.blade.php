<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donation Receipt</title>
    <style>
        @page { margin: 28px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111827;
            position: relative;
        }
        .watermark {
            position: fixed;
            top: 43%;
            left: 8%;
            width: 84%;
            text-align: center;
            font-size: 58px;
            color: rgba(29, 140, 248, 0.08);
            letter-spacing: 4px;
            transform: rotate(-25deg);
            z-index: -1;
        }
        .top {
            width: 100%;
            border-bottom: 2px solid #1d8cf8;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }
        .logo-box {
            display: inline-block;
            width: 38%;
            vertical-align: top;
        }
        .org-box {
            display: inline-block;
            width: 60%;
            text-align: right;
            vertical-align: top;
        }
        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin: 16px 0 10px;
        }
        .receipt-no {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 18px;
        }
        .block {
            border: 1px solid #d1d5db;
            background: #f9fafb;
            padding: 12px 14px;
            margin-bottom: 14px;
        }
        .row { margin: 7px 0; }
        .label { font-weight: bold; }
        .purpose {
            margin-top: 16px;
            border: 1px solid #d1d5db;
            padding: 12px 14px;
        }
        .signature {
            margin-top: 52px;
            text-align: right;
        }
        .line {
            display: inline-block;
            width: 220px;
            border-bottom: 1px solid #111827;
            margin-bottom: 6px;
        }
        .logo {
            max-height: 60px;
        }
    </style>
</head>
<body>
    <div class="watermark">HOUR OF LIGHT</div>

    <div class="top">
        <div class="logo-box">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" alt="Hour of Light Logo" class="logo">
            @else
                <!-- Logo placeholder: add public/images/logo.png -->
            @endif
        </div>
        <div class="org-box">
            <div style="font-size:16px;font-weight:bold;">Hour of Light</div>
            <div>houroflight.com</div>
            <div>receipts@houroflight.com</div>
        </div>
    </div>

    <div class="title">Donation Acknowledgement Receipt</div>
    <div class="receipt-no">Receipt No: {{ $donation->receipt_number ?: '-' }}</div>

    <div class="block">
        <div class="row"><span class="label">Donor Name:</span> {{ $donation->donor_name }}</div>
        <div class="row"><span class="label">Donor Email:</span> {{ $donation->donor_email }}</div>
        <div class="row"><span class="label">Amount:</span> {{ number_format((float) $donation->amount, 2) }}</div>
        <div class="row"><span class="label">Currency:</span> {{ $donation->currency }}</div>
        <div class="row"><span class="label">Verified Date:</span> {{ optional($donation->verified_at)->format('F j, Y') ?: now()->format('F j, Y') }}</div>
    </div>

    <div class="purpose">
        <div class="label">Purpose:</div>
        <div style="margin-top:5px;">
            To support schools with teacher training, school systems, and EdTech classroom upgrades.
        </div>
    </div>

    <div class="signature">
        <div class="line"></div>
        <div>Authorized Signatory</div>
        <div>Hour of Light Administration</div>
    </div>
</body>
</html>
