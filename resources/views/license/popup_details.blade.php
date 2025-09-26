<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>License Details</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #4e6174;
            padding: 30px;
        }

        .container {
            max-width: 382px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 30px 40px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .label {
            font-weight: 600;
            color: #555;
            width: 40%;
        }

        .value {
            color: #222;
            width: 55%;
        }

        ol {
            padding-left: 20px;
            margin: 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
        }

        .footer button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .footer button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>License Details</h2>

        <div class="row">
            <div class="label">License Name:</div>
            <div class="value">{{ $license->licenseName->license_name }}</div>
        </div>
        <div class="row">
            <div class="label">License Type:</div>
            <div class="value">{{ $license->licenseType->license_type }}</div>
        </div>
        <div class="row">
            <div class="label">Date of Issue:</div>
            <div class="value">{{ \Carbon\Carbon::parse($license->valid_from)->format('d-m-Y') }}</div>
        </div>
        <div class="row">
            <div class="label">Valid Upto:</div>
            <div class="value" style="color: red;">{{ \Carbon\Carbon::parse($license->valid_upto)->format('d-m-Y') }}</div>
        </div>
        <div class="row" style="align-items: flex-start;">
            <div class="label">Reminder Emails:</div>
            <div class="value">
                <ol>
                    @foreach(explode(',', $license->reminder_emails) as $email)
                        @if(trim($email))
                            <li>{{ trim($email) }}</li>
                        @endif
                    @endforeach
                </ol>
            </div>
        </div>

        <div class="footer">
            <button onclick="window.close();">Close</button>
        </div>
    </div>

</body>
</html>
