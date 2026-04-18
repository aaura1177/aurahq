<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>🎉 Birthday Reminder</h2>
<p>The following employees have a birthday tomorrow:</p>
<ul>
    @foreach($users as $user)
        <li>{{ $user->name }} ({{ \Carbon\Carbon::parse($user->date_of_birth)->format('d M') }})</li>
    @endforeach
</ul>

</body>
</html>