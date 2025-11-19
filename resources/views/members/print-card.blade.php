<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Member - {{ $member->name }}</title>
    <style>
        @page {
            size: 85.6mm 53.98mm;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }

        .card-container {
            width: 85.6mm;
            height: 53.98mm;
            margin: 0 auto 30px;
        }

        .card {
            width: 100%;
            height: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        /* DEPAN */
        .card-front {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px;
            color: white;
            position: relative;
        }

        .logo-area {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 12px;
        }

        .logo {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #667eea;
        }

        .library-name h2 {
            font-size: 14px;
            margin-bottom: 2px;
        }

        .library-name p {
            font-size: 9px;
            opacity: 0.9;
        }

        .member-photo {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 60px;
            height: 75px;
            border: 3px solid white;
            border-radius: 6px;
            overflow: hidden;
            background: white;
        }

        .member-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .member-info {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .member-id {
            font-size: 11px;
            margin-bottom: 4px;
        }

        .member-name {
            font-size: 16px;
            font-weight: 700;
        }

        .barcode-section {
            background: white;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
        }

        .barcode {
            height: 40px;
            background: repeating-linear-gradient(90deg, #000 0px, #000 2px, #fff 2px, #fff 4px);
            margin-bottom: 4px;
        }

        .barcode-number {
            font-size: 10px;
            color: #333;
            font-weight: 600;
        }

        /* BELAKANG */
        .card-back {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 15px;
        }

        .back-header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
        }

        .back-header h3 {
            font-size: 13px;
            color: #667eea;
            margin-bottom: 3px;
        }

        .rules ol {
            padding-left: 15px;
            margin: 0 0 10px 0;
        }

        .rules li {
            font-size: 7.5px;
            margin-bottom: 3px;
            line-height: 1.3;
        }

        .contact-info {
            background: rgba(102, 126, 234, 0.1);
            padding: 6px;
            border-radius: 6px;
        }

        .contact-info h4 {
            font-size: 9px;
            color: #667eea;
            margin-bottom: 4px;
        }

        .contact-item {
            font-size: 7.5px;
            margin-bottom: 2px;
        }

        .found-notice {
            text-align: center;
            font-size: 7px;
            color: #e53e3e;
            margin-top: 6px;
            font-weight: 600;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .card-container {
                page-break-after: always;
            }
            .no-print {
                display: none;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<!-- DEPAN -->
<div class="card-container">
    <div class="card">
        <div class="card-front">
            <div class="logo-area">
                <div class="logo">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="library-name">
                    <h2>BIMANTARA PUSTAKA</h2>
                    <p>Member Card - Kartu Anggota</p>
                </div>
            </div>

            <div class="member-photo">
                @if($member->photo_path)
                    <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&size=200&background=667eea&color=fff" alt="{{ $member->name }}">
                @endif
            </div>

            <div class="member-info">
                <div class="member-id">ID: {{ $member->formatted_id }}</div>
                <div class="member-name">{{ strtoupper($member->name) }}</div>
            </div>

            <div class="barcode-section">
                <div class="barcode"></div>
                <div class="barcode-number">{{ $member->barcode }}</div>
            </div>
        </div>
    </div>
</div>

<!-- BELAKANG -->
<div class="card-container">
    <div class="card">
        <div class="card-back">
            <div class="back-header">
                <h3>TATA TERTIB PEMINJAMAN</h3>
                <p>Library Membership Rules</p>
            </div>

            <div class="rules">
                <ol>
                    <li>Kartu member harus dibawa setiap berkunjung ke perpustakaan</li>
                    <li>Kartu member tidak boleh dipinjamkan atau digunakan oleh pihak lain</li>
                    <li>Peminjaman maksimal 3 (tiga) buku dengan jangka waktu 1 minggu</li>
                    <li>Perpanjangan peminjaman maksimal 1x dengan jangka waktu 1 minggu</li>
                    <li>Perubahan alamat/nomor telepon harus segera dilaporkan ke perpustakaan</li>
                    <li>Anggota wajib mematuhi segala peraturan yang berlaku di perpustakaan</li>
                    <li><strong>Jika menemukan kartu ini, harap dikembalikan ke perpustakaan</strong></li>
                </ol>
            </div>

            <div class="contact-info">
                <h4><i class="fas fa-headset"></i> HUBUNGI KAMI</h4>
                <div class="contact-item">📞 CS: 0271-123456</div>
                <div class="contact-item">✉️ info@bimantarapustaka.com</div>
                <div class="contact-item">📷 @bimantarapustaka</div>
                <div class="contact-item">📘 Bimantara Pustaka Official</div>
                <div class="contact-item">📍 Jl. Perpustakaan No. 123, Surakarta</div>
            </div>

            <div class="found-notice">
                ⚠️ Kartu ini adalah milik Perpustakaan Bimantara Pustaka
            </div>
        </div>
    </div>
</div>

<div class="no-print" style="text-align: center; margin-top: 20px;">
    <button onclick="window.print()" style="background: #667eea; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-size: 14px;">
        <i class="fas fa-print"></i> Cetak Kartu
    </button>
    <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-size: 14px; margin-left: 10px;">
        <i class="fas fa-times"></i> Tutup
    </button>
</div>

<script>
    // Auto print on load (optional)
    // window.onload = function() {
    //     setTimeout(() => window.print(), 500);
    // };
</script>
</body>
</html>
