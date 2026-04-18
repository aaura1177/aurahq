<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>New Leave Request Submitted</h2>

<p><strong>Employee Name:</strong> {{ $leave->employee->name }}</p>
<p><strong>Leave Type:</strong>  {{ $leave->leaveType->leave_name }}</p>

<p><strong>Start Date:</strong> {{ $leave['start_at'] }}</p>
<p><strong>End Date:</strong> {{ $leave['end_at'] }}</p>
<p><strong>Reason:</strong> {{ $leave['reason'] }}</p>

</body>
</html>