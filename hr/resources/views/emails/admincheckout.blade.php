<!DOCTYPE html>
<html>
<head>
    <title>Employee Checkout</title>
</head>
<body>
    <h2>Employee Checkout Completed</h2>

    <p><strong>Employee:</strong> {{ $attendance->employee->name }}</p>
    <p><strong>Date:</strong> {{ $attendance->date }}</p>
    <p><strong>Check-In Time:</strong> {{ $attendance->check_in_time }}</p>
    <p><strong>Check-Out Time:</strong> {{ $attendance->check_out_time }}</p>
    <p><strong>Working Hours:</strong> {{ $attendance->working_hours }}</p>
    <p><strong>Overtime:</strong> {{ $attendance->overtime_hours }}</p>
    <!-- <p><strong>Earned Salary:</strong> ₹{{ $attendance->earned_salary }}</p> -->
</body>
</html>
