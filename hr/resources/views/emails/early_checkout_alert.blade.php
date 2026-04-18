<!DOCTYPE html>
<html>
<head>
    <title>Early Checkout Alert</title>
</head>
<body>
    <h3>Early Checkout Notification</h3>
    <p>Employee <strong>{{ $employee->name }}</strong> (ID: {{ $employee->id }}) has checked out early today.</p>
    <p><strong>Checkout Time:</strong> {{ $checkOutTime->format('H:i:s') }}</p>
    <p>Date: {{ \Carbon\Carbon::today()->toDateString() }}</p>
</body>
</html>
