<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Hour of Light</title>
</head>
<body style="margin:0; padding:0; background:#f5f7fb; font-family:Arial, sans-serif; color:#111827;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f7fb; padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px;">
                <tr>
                    <td style="padding:28px;">
                        <h1 style="margin:0 0 16px; font-size:24px; line-height:1.2; color:#111827;">Welcome to Hour of Light</h1>
                        <p style="margin:0 0 12px; font-size:15px; line-height:1.6;">
                            Hello {{ $recipientName }},
                        </p>
                        <p style="margin:0 0 12px; font-size:15px; line-height:1.6;">
                            Welcome to Hour of Light. Your {{ strtolower($accountType) }} account has been registered and approved by the admin team.
                        </p>
                        <p style="margin:0 0 12px; font-size:15px; line-height:1.6;">
                            You can now sign in and continue with your journey on Hour of Light.
                        </p>
                        <p style="margin:16px 0 0; font-size:15px; line-height:1.6;">
                            Regards,<br>
                            Hour of Light Team
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
