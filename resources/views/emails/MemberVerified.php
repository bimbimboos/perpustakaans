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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
        }
        .verification-code {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            margin: 30px 0;
        }
        .code {
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 10px;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
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
        <h1>✅ Akun Terverifikasi!</h1>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $memberName }}</strong>,</p>
        <p>Selamat! Akun perpustakaan Anda telah berhasil diverifikasi oleh admin.</p>

        <div class="verification-code">
            <p style="margin: 0; font-size: 14px;">Kode Verifikasi Anda:</p>
            <div class="code">{{ $verificationCode }}</div>
            <p style="margin: 0; font-size: 12px;">Simpan kode ini untuk keperluan login</p>
        </div>

        <p>Anda sekarang dapat:</p>
        <ul>
            <li>✅ Login ke sistem perpustakaan</li>
            <li>📚 Meminjam buku</li>
            <li>📖 Mengakses katalog digital</li>
            <li>📧 Menerima notifikasi peminjaman</li>
        </ul>

        <p style="text-align: center;">
            <a href="{{ $loginUrl }}" class="btn">
                🚀 Login Sekarang
            </a>
        </p>
    </div>

    <div class="footer">
        <p>© 2025 Perpustakaan MboLali. All rights reserved.</p>
        <p>Jika Anda tidak mendaftar, abaikan email ini.</p>
    </div>
</div>
</body>
</html>
