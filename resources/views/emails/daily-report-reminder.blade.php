<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #334155; }
        .container { max-width: 560px; margin: 0 auto; padding: 20px; }
        h2 { color: #1e293b; }
        .btn { display: inline-block; padding: 10px 20px; background: #2563eb; color: #fff !important; text-decoration: none; border-radius: 8px; margin-top: 12px; }
        .footer { margin-top: 24px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reminder – daily report not yet submitted</h2>
        <p>Hi {{ $user->name }},</p>
        <p>You have not yet submitted your <strong>{{ $slot }}</strong> daily report for <strong>{{ $date }}</strong>.</p>
        <p>Please submit by the deadline: <strong>{{ $slot === 'morning' ? '11:00 AM' : '5:15 PM' }} IST</strong> to avoid a disciplinary notice.</p>
        <p><strong>Reporting windows (IST):</strong></p>
        <ul>
            <li>Morning: till 11:00 AM</li>
            <li>Evening: till 5:15 PM (on present days only)</li>
        </ul>
        <p>Submit from the Daily Reports section.</p>
        <a href="{{ config('app.url') }}/daily-reports/create" class="btn">Submit report</a>
        <div class="footer">{{ config('app.name') }}</div>
    </div>
</body>
</html>
