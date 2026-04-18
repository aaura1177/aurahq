<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>New Work From Home Request</h2>


<p><strong>Employee Name:</strong> {{ $data->user->name }}</p>
<p><strong>Date:</strong> {{ $data->work_date }}</p>
<p><strong>Start Time:</strong> {{ $data->start_time }}</p>
<p><strong>End Time:</strong> {{ $data->end_time }}</p>
<p><strong>Reason:</strong> {{ $data->reason }}</p>
<p><strong>Location:</strong> {{ $data->location }}</p>

</body>
</html>