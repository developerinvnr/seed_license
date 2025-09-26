{{-- <!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #f8d7da;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h2 {
            margin: 0;
            color: #721c24;
        }
        .license-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .license-item:last-child {
            border-bottom: none;
        }
        .license-item p {
            margin: 5px 0;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Expired License Notification</h2>
        </div>
        <p>Dear User,</p>
        <p>The following licenses have expired. Please review and assign new responsible persons as needed.</p>

        @foreach ($expiredLicenses as $license)
            <div class="license-item">
                <p><strong>Employee:</strong> {{ $license['emp_name'] }}</p>
                <p><strong>Manager:</strong> {{ $license['manager_name'] }}</p>
                <p><strong>Company:</strong> {{ $license['company_name'] }}</p>
                <p><strong>License Type:</strong> {{ $license['license_type'] }}</p>
                <p><strong>License Name:</strong> {{ $license['license_name'] }}</p>
                @if ($license['certificate_no'] !== 'N/A')
                    <p><strong>Certificate No:</strong> {{ $license['certificate_no'] }}</p>
                @endif
                <p><strong>Expired On:</strong> {{ $license['valid_up_to'] }}</p>
                <a href="{{ url('/responsible') }}" class="btn">Assign New Person</a>
            </div>
        @endforeach

        <div class="footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Your Company Name.</p>
        </div>
    </div>
</body>
</html> --}}


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1a202c;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #f7fafc;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }
        .content {
            padding: 24px;
        }
        .content p {
            margin: 0 0 16px;
            font-size: 16px;
            color: #4a5568;
        }
        .license-item {
            border-top: 1px solid #edf2f7;
            padding: 16px 0;
        }
        .license-item:last-child {
            border-bottom: none;
        }
        .license-item p {
            margin: 8px 0;
            font-size: 14px;
        }
        .license-item strong {
            color: #2d3748;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3182ce;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 12px;
        }
        .btn:hover {
            background-color: #2b6cb0;
        }
        .footer {
            background-color: #f7fafc;
            padding: 16px;
            text-align: center;
            font-size: 12px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Authorization person Expired License Notification</h1>
        </div>
        <div class="content">
            <p>Dear User,</p>
            <p>The following Assign licenses have valid up to expired. Please review and assign new responsible persons as needed.</p>

            @foreach ($expiredLicenses as $license)
                <div class="license-item">
                    <p><strong>Employee:</strong> {{ $license['emp_name'] }}</p>
                    <p><strong>Reporting Manager:</strong> {{ $license['manager_name'] }}</p>
                    <p><strong>Company:</strong> {{ $license['company_name'] }}</p>
                    <p><strong>License Type:</strong> {{ $license['license_type'] }}</p>
                    <p><strong>License Name:</strong> {{ $license['license_name'] }}</p>
                    @if ($license['certificate_no'] !== 'N/A')
                        <p><strong>Certificate No:</strong> {{ $license['certificate_no'] }}</p>
                    @endif
                    <p><strong>Expired On:</strong> {{ $license['valid_up_to'] }}</p>
                    <a href="{{ url('/responsible') }}" class="btn">Assign New Person</a>
                </div>
            @endforeach
        </div>
        <div class="footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Your Company Name.</p>
        </div>
    </div>
</body>
</html>