<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-row {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-label {
            font-weight: bold;
            color: #667eea;
            display: inline-block;
            width: 150px;
        }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔔 Pendaftar Member Baru</h1>
    </div>

    <div class="content">
        <p>Halo Admin Perpustakaan,</p>
        <p>Ada pendaftar member baru yang memerlukan verifikasi:</p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Nama Lengkap:</span>
                <span>{{ $memberName }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span>{{ $memberEmail }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">No. Telepon:</span>
                <span>{{ $memberPhone }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Alamat:</span>
                <span>{{ $memberAddress }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Daftar:</span>
                <span>{{ $registeredAt }}</span>
            </div>
        </div>

        <p style="text-align: center;">
            <a href="{{ $verifyUrl }}" class="btn">
                ✅ Verifikasi Pendaftar
            </a>
        </p>

        <p style="color: #999; font-size: 12px;">
            Atau salin link ini ke browser: <br>
            <a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a>
        </p>
    </div>

    <div class="footer">
        <p>© 2025 Perpustakaan MboLali. All rights reserved.</p>
    </div>
</div>
</body>
</html>
