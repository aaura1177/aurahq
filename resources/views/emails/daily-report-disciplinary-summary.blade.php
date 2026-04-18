<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #334155; }
        .container { max-width: 560px; margin: 0 auto; padding: 20px; }
        h2 { color: #b91c1c; }
        ul { padding-left: 20px; }
        .notice { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin: 16px 0; color: #991b1b; }
        .footer { margin-top: 24px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Disciplinary – missing {{ $slot }} reports</h2>
        <p>The following employee(s) did not submit their <strong>{{ $slot }}</strong> report for <strong>{{ $date }}</strong> by the final deadline. Reminder had already been sent.</p>
        <ul>
            @foreach($missingNames as $name)
            <li>{{ $name }}</li>
            @endforeach
        </ul>
        <div class="notice">
            <strong>Disciplinary notices</strong> have been sent to the above employee(s).
        </div>
        <div class="footer">{{ config('app.name') }}</div>
    </div>
</body>
</html>
