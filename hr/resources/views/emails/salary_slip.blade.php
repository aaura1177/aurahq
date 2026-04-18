<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>Salary Slip - {{ $salaryMonth->format('F Y') }}</h2>

<p><strong>Employee:</strong> {{ $employee->name }}</p>
<p><strong>Email:</strong> {{ $employee->email }}</p>
<p><strong>Net Salary:</strong> ₹{{ number_format($netSalary, 2) }}</p>

<p>Thank you for your hard work!</p>

</body>
</html>