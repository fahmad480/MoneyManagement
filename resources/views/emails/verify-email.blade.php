<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .header h1 {
            color: #3b82f6;
            margin: 0;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #3b82f6;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .note {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 10px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            <h2>Halo, {{ $user->name }}!</h2>
            <p>Terima kasih telah mendaftar di {{ config('app.name') }}. Untuk melengkapi pendaftaran Anda, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah ini:</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Verifikasi Email Saya</a>
            </div>
            
            <div class="note">
                <strong>Catatan:</strong> Link verifikasi ini akan kedaluwarsa dalam 60 menit.
            </div>
            
            <p>Jika Anda tidak membuat akun ini, tidak ada tindakan lebih lanjut yang diperlukan.</p>
            
            <p>Jika Anda mengalami masalah dengan tombol di atas, salin dan tempel URL berikut ke browser Anda:</p>
            <p style="word-break: break-all; color: #3b82f6;">{{ $verificationUrl }}</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
