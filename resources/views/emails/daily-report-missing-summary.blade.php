<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #334155; }
        .container { max-width: 560px; margin: 0 auto; padding: 20px; }
        h2 { color: #1e293b; }
        ul { padding-left: 20px; }
        .footer { margin-top: 24px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daily report – missing {{ $slot }} reports (reminder)</h2>
        <p>The following employee(s) had not submitted their <strong>{{ $slot }}</strong> report for <strong>{{ $date }}</strong> by {{ $slot === 'morning' ? '10:20 AM' : '5:00 PM' }} IST:</p>
        <ul>
            @foreach($missingNames as $name)
            <li>{{ $name }}</li>
            @endforeach
        </ul>
        <p>Reminder emails have been sent to them. If still not submitted by {{ $slot === 'morning' ? '11:00 AM' : '5:15 PM' }} IST, a disciplinary notice will be sent.</p>
        <div class="footer">{{ config('app.name') }}</div>
    </div>
</body>
</html>
