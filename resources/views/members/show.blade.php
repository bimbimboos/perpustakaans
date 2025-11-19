@extends('layouts.app')

@section('title', 'Detail Member')

@push('styles')
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #38a169;
            --warning: #dd6b20;
            --danger: #e53e3e;
            --info: #3182ce;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 2rem;
            margin: -30px -30px 2rem -30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 0.75rem;
        }

        .page-header h1 {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 1.75rem;
        }

        .breadcrumb {
            background: transparent;
            margin: 0.5rem 0 0 0;
            padding: 0;
        }

        .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: white;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.6);
        }

        .member-card {
            background: var(--card-bg);
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid var(--card-border);
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 1.5rem;
            color: white;
        }

        .card-header-custom h5 {
            margin: 0;
            font-weight: 600;
        }

        .photo-section {
            text-align: center;
            padding: 2rem;
            background: var(--table-header-bg);
            transition: background-color 0.3s;
        }

        .member-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 5px solid var(--card-bg);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            object-fit: cover;
            margin-bottom: 1rem;
        }

        .info-table {
            width: 100%;
        }

        .info-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--card-border);
            color: var(--body-color);
            transition: color 0.3s, border-color 0.3s;
        }

        .info-table td:first-child {
            font-weight: 600;
            color: var(--sidebar-color);
            width: 200px;
            opacity: 0.8;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-block;
        }

        .badge-verified {
            background: #f0fff4;
            color: var(--success);
        }

        .badge-pending {
            background: #fffaf0;
            color: var(--warning);
        }

        .badge-rejected {
            background: #fff5f5;
            color: var(--danger);
        }

        .btn-action {
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .ktp-preview {
            max-width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .verification-code-display {
            background: linear-gradient(135deg, var(--success) 0%, #48bb78 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            text-align: center;
            margin: 1rem;
        }

        .verification-code-display .code {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 0.5rem;
            margin: 0.5rem 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        @media (max-width: 768px) {
            .page-header {
                margin: -30px -15px 1.5rem -15px;
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .info-table td:first-child {
                width: 100%;
                display: block;
                padding-bottom: 0.25rem;
            }
            .info-table td:last-child {
                width: 100%;
                display: block;
                padding-top: 0.25rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header fade-in">
        <h1><i class="fas fa-user-circle me-2"></i> Detail Member</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('members.index') }}">Members</a></li>
                <li class="breadcrumb-item active">{{ $member->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="member-card fade-in">
                <div class="photo-section">
                    @if($member->photo_path)
                        <img src="{{ route('members.download-photo', $member->id_member) }}"
                             alt="{{ $member->name }}"
                             class="member-photo">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&size=200&background=667eea&color=fff"
                             alt="{{ $member->name }}"
                             class="member-photo">
                    @endif

                    <h4 class="mb-2">{{ $member->name }}</h4>
                    <p class="text-muted mb-3">
                        <i class="fas fa-id-badge me-1"></i>
                        ID: #{{ str_pad($member->id_member, 6, '0', STR_PAD_LEFT) }}
                    </p>

                    <!-- Status Badge -->
                    @if($member->status === 'verified')
                        <span class="badge-status badge-verified">
                        <i class="fas fa-check-circle me-1"></i> Terverifikasi
                    </span>
                    @elseif($member->status === 'pending')
                        <span class="badge-status badge-pending">
                        <i class="fas fa-clock me-1"></i> Menunggu Verifikasi
                    </span>
                    @else
                        <span class="badge-status badge-rejected">
                        <i class="fas fa-times-circle me-1"></i> Ditolak
                    </span>
                    @endif
                </div>

                <!-- Verification Code (if verified) -->
                @if($member->status === 'verified' && $member->verification_code)
                    <div class="verification-code-display">
                        <small>Kode Verifikasi:</small>
                        <div class="code">{{ $member->verification_code }}</div>
                        <small><i class="fas fa-info-circle me-1"></i> Simpan kode ini</small>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="member-card fade-in">
                <div class="p-3 d-grid gap-2">
                    @if(auth()->user()->role === 'admin' || auth()->user()->id_user === $member->id_user)
                        <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#editMemberModal">
                            <i class="fas fa-edit me-2"></i> Edit Data
                        </button>
                    @endif

                    @if(auth()->user()->role === 'admin' && $member->status === 'pending')
                        <form action="{{ route('members.verify-manual', $member->id_member) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-action w-100"
                                    onclick="return confirm('Verifikasi member ini?')">
                                <i class="fas fa-check me-2"></i> Verifikasi Sekarang
                            </button>
                        </form>

                        <form action="{{ route('members.reject', $member->id_member) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-action w-100"
                                    onclick="return confirm('Tolak member ini?')">
                                <i class="fas fa-times me-2"></i> Tolak Pendaftar
                            </button>
                        </form>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <form action="{{ route('members.destroy', $member->id_member) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-action w-100"
                                    onclick="return confirm('Yakin hapus member ini?')">
                                <i class="fas fa-trash me-2"></i> Hapus Member
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('members.index') }}" class="btn btn-secondary btn-action">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Information -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="member-card fade-in">
                <div class="card-header-custom">
                    <h5><i class="fas fa-user me-2"></i> Informasi Pribadi</h5>
                </div>
                <div class="p-3">
                    <table class="info-table">
                        <tr>
                            <td>Nama Lengkap</td>
                            <td><strong>{{ $member->name }}</strong></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $member->email }}</td>
                        </tr>
                        <tr>
                            <td>No. Telepon</td>
                            <td>{{ $member->no_telp }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>{{ $member->alamat }}</td>
                        </tr>
                        <tr>
                            <td>Role</td>
                            <td><span class="badge bg-info">{{ ucfirst($member->role) }}</span></td>
                        </tr>
                        <tr>
                            <td>Tanggal Daftar</td>
                            <td>{{ $member->created_at->format('d F Y, H:i') }} WIB</td>
                        </tr>
                        @if($member->admin_verified_at)
                            <tr>
                                <td>Diverifikasi Pada</td>
                                <td>{{ $member->admin_verified_at->format('d F Y, H:i') }} WIB</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- KTP Information (Admin Only) -->
            @if(($canViewKtp ?? false) || auth()->user()->role === 'admin')
                <div class="member-card fade-in">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-id-card me-2"></i> Informasi KTP</h5>
                    </div>
                    <div class="p-3">
                        @if($member->ktp_photo_path)
                            <div class="mb-3">
                                <img src="{{ route('members.download-ktp', $member->id_member) }}"
                                     alt="KTP"
                                     class="ktp-preview">
                            </div>
                            <a href="{{ route('members.download-ktp', $member->id_member) }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-download me-2"></i> Download KTP
                            </a>
                        @else
                            <p class="text-muted">Foto KTP belum diupload</p>
                        @endif

                        @if($member->ktp_verified_at)
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle me-2"></i>
                                KTP terverifikasi pada {{ $member->ktp_verified_at->format('d F Y, H:i') }}
                            </div>
                        @else
                            <form action="{{ route('members.verify-ktp', $member->id_member) }}"
                                  method="POST" class="mt-3">
                                @csrf
                                <input type="hidden" name="verified" value="1">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check me-2"></i> Verifikasi KTP
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Borrowing History -->
            @if(isset($member->borrowing) && $member->borrowing->count() > 0)
                <div class="member-card fade-in">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-book-reader me-2"></i> Riwayat Peminjaman (10 Terakhir)</h5>
                    </div>
                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Status</th>
                                    <th>Kondisi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($member->borrowing as $index => $borrow)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $borrow->books->judul ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($borrow->pinjam)->format('d M Y') }}</td>
                                        <td>
                                            @if($borrow->status === 'dipinjam')
                                                <span class="badge bg-warning">Dipinjam</span>
                                            @elseif($borrow->status === 'kembali')
                                                <span class="badge bg-success">Kembali</span>
                                            @else
                                                <span class="badge bg-info">{{ ucfirst($borrow->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($borrow->kondisi === 'baik')
                                                <span class="badge bg-success">Baik</span>
                                            @else
                                                <span class="badge bg-danger">{{ ucfirst($borrow->kondisi) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- MODAL EDIT MEMBER -->
    <div class="modal fade" id="editMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('members.update', $member->id_member) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control"
                                   value="{{ $member->no_telp }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" required>{{ $member->alamat }}</textarea>
                        </div>

                        <!-- field lain sengaja tidak ditampilkan karena TIDAK BOLEH diubah -->

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- END MODAL -->

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            timer: 3000,
            showConfirmButton: false
        });
        @endif
    </script>
@endpush
