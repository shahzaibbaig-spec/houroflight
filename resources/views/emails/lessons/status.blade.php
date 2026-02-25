<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson Update</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#1d8cf8;color:#ffffff;padding:18px 24px;font-size:20px;font-weight:700;">
                            Hour of Light - Lesson Notification
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            @if(!empty($customMessage))
                                <p style="margin:0 0 16px 0;line-height:1.6;">{{ $customMessage }}</p>
                            @endif

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                <tr>
                                    <td style="padding:10px;border:1px solid #e5e7eb;background:#f9fafb;font-weight:600;">Lesson Title</td>
                                    <td style="padding:10px;border:1px solid #e5e7eb;">{{ $lesson->title }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px;border:1px solid #e5e7eb;background:#f9fafb;font-weight:600;">Status</td>
                                    <td style="padding:10px;border:1px solid #e5e7eb;text-transform:uppercase;">{{ $lesson->status }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px;border:1px solid #e5e7eb;background:#f9fafb;font-weight:600;">Subject</td>
                                    <td style="padding:10px;border:1px solid #e5e7eb;">{{ $lesson->subject }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px;border:1px solid #e5e7eb;background:#f9fafb;font-weight:600;">Grades</td>
                                    <td style="padding:10px;border:1px solid #e5e7eb;">{{ $lesson->grade_min }} - {{ $lesson->grade_max }}</td>
                                </tr>
                                @if(!empty($lesson->review_notes))
                                    <tr>
                                        <td style="padding:10px;border:1px solid #e5e7eb;background:#f9fafb;font-weight:600;">Review Notes</td>
                                        <td style="padding:10px;border:1px solid #e5e7eb;">{{ $lesson->review_notes }}</td>
                                    </tr>
                                @endif
                            </table>

                            @if(!empty($adminPanelLink))
                                <p style="margin:20px 0 0 0;">
                                    <a href="{{ $adminPanelLink }}" style="display:inline-block;background:#1d8cf8;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:8px;font-weight:600;">
                                        Open Admin Review
                                    </a>
                                </p>
                            @endif

                            <p style="margin:20px 0 0 0;color:#6b7280;font-size:13px;line-height:1.5;">
                                This is an automated notification from Hour of Light.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

