<!DOCTYPE html>
<html>
<head>
    <title>Pending Tasks Notification</title>
</head>
<body>
    <h1>Pending Tasks Overdue</h1>
    <p>The following tasks have been pending for over 5 days and have been marked as overdue:</p>
    
@foreach ($tasks as $task)
    <div style="margin-bottom: 15px; padding: 10px; background-color: #f9f9f9; border-left: 4px solid #ff6b6b;">
        <p><strong>Employee Name:</strong> {{ $task->employee->name ?? 'N/A' }}</p>
        <p><strong>Task Name:</strong> {{ $task->name }}</p>
        <p><strong>Status:</strong> {{ $task->employee_status }}</p>
    </div>
@endforeach
   <!-- <ol>
    @foreach ($tasks as $task)
        <li>
            User: {{ $task->employee->name ?? 'N/A' }}, 
            Task: {{ $task->name }}, 
            Status: {{ $task->employee_status }}
        </li>
    @endforeach
</ol> -->

</body>
</html>
