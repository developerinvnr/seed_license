<!DOCTYPE html>
<html>
<head>
    <title>License Expiry Reminder</title>
</head>
<body>
    <p>Hello {{ $details['user'] }},</p>

    <p>The following licenses are expiring today:</p>

    <ul>
        @foreach($details['licenses'] as $license)
            <li>
                Responsible: <strong>{{ $license->responsible_name }}</strong><br>
                License Type: <strong>{{ $license->license_type }}</strong><br>
                License Name: <strong>{{ $license->license_name }}</strong><br>
                Expiry Date: <strong>{{ $license->authorized_expired_date }}</strong>
            </li>
        @endforeach
    </ul>

    <p>Please take necessary action.</p>
</body>
</html>
