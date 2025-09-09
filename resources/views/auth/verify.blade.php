<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 16px; color: #222;">
    <h2 style="color: #007bff;">Email Verification</h2>
    <p>Your OTP for email verification is: <strong style="font-size:18px;">{{ $otp }}</strong></p>
    <p style="color: #555;">It will expire in <strong>10 minutes</strong>.</p>
    <p>
        Please <a href="{{ $verifyUrl }}" style="color: #007bff; text-decoration: underline;">click here to verify your email</a>.
    </p>
    <hr>
    <p style="font-size:14px; color:#888;">After verification, you can login.</p>
</body>
</html>
