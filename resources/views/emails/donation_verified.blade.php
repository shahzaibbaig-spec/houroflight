<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Verified</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f7fb;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;width:100%;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#1d8cf8;color:#ffffff;padding:18px 24px;">
                            <h1 style="margin:0;font-size:20px;line-height:1.3;">Hour of Light</h1>
                            <p style="margin:6px 0 0 0;font-size:13px;opacity:0.95;">Donation Received and Verified</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px 0;font-size:15px;">Dear {{ $donation->donor_name }},</p>
                            <p style="margin:0 0 14px 0;font-size:14px;line-height:1.7;">
                                Thank you for your generous support. We confirm that your donation has been received and verified.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;">
                                <tr>
                                    <td style="padding:14px 16px;">
                                        <p style="margin:0 0 8px 0;font-size:13px;"><strong>Receipt Number:</strong> {{ $donation->receipt_number ?: '-' }}</p>
                                        <p style="margin:0 0 8px 0;font-size:13px;"><strong>Amount:</strong> {{ number_format((float) $donation->amount, 2) }} {{ $donation->currency }}</p>
                                        <p style="margin:0;font-size:13px;"><strong>Verified Date:</strong> {{ optional($donation->verified_at)->format('F j, Y') ?: now()->format('F j, Y') }}</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:16px 0 0 0;font-size:14px;line-height:1.7;">
                                We appreciate your trust and support in helping us create better learning opportunities for underserved schools.
                            </p>
                            <p style="margin:18px 0 0 0;font-size:14px;line-height:1.7;">
                                With gratitude,<br>
                                <strong>Hour of Light Administration</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
