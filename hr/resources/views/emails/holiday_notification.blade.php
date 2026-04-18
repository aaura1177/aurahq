<!DOCTYPE html>
<html>
<head>
    <title>Holiday Notification</title>
</head>
<body>
    <h1>Holiday Notification</h1>
    <p>Dear Employee,</p>
    <p>We would like to inform you that <strong>{{ $holidayName }}</strong> is on <strong>{{ $holidayDate }}</strong>.</p>

    @if(!empty($remark))
        <p><strong>Note:</strong> {{ $remark }}</p>
    @endif

    <p>Please plan accordingly.</p>
    <p>Best Regards,<br>Aurateria</p>
</body>
</html>
