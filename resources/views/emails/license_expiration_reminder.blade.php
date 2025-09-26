<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Expiration Reminder</title>
</head>

<body>
    <h3>Hello,</h3>
    <p>This is a reminder that the license with the following details is about to expire:</p>

    <p><strong>License Type:</strong> {{ $license->licenseType->license_type }}<br>
        <strong>License Name:</strong> {{ $license->licenseName->license_name }}<br>
        <strong>Valid Upto:</strong> {{ $license->valid_upto }}
    </p>

    <p>
        <a href="{{ url('/license/' . $license->id) }}" target="_blank"
            style="padding: 8px 12px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">
            👉 View License Details
        </a>
    </p>


    <p>Please take the necessary actions to renew it.</p>

    <p>Thank you,<br>License Tracking System</p>
</body>

</html>
