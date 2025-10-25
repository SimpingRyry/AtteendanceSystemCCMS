<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Biometric Registration Schedule</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <p>Dear {{ $student->name }},</p>

    <p>We are pleased to inform you that you are <strong>scheduled for biometric registration</strong> on 
    <strong>{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</strong>.</p>

<p>Please arrive between 8:00 AM to 5:00 PM during your free time.</p>

    <p>Thank you and see you soon!</p>

    <p>Best regards,</p>
    <p><strong>{{ $orgName }}</strong><br>
</body>
</html>
