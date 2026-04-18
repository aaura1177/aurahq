<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #334155; }
        .container { max-width: 560px; margin: 0 auto; padding: 20px; }
        h2 { color: #b91c1c; }
        .notice { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin: 16px 0; color: #991b1b; }
        .footer { margin-top: 24px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Disciplinary notice – daily report not submitted</h2>
        <p>Dear {{ $user->name }},</p>
        <p>You failed to submit your <strong>{{ $slot }}</strong> daily report for <strong>{{ $date }}</strong> by the final deadline (11:00 AM for morning, 5:15 PM for evening IST). A reminder had already been sent to you.</p>
        <div class="notice">
            <strong>This is a disciplinary notice.</strong> Repeated failure to submit daily reports on time may result in further action as per company policy.
        </div>
        <p>Please ensure you submit your morning report by 11:00 AM IST and evening report (on present days) by 5:15 PM IST going forward.</p>
        <div class="footer">{{ config('app.name') }}</div>
    </div>
</body>
</html>
