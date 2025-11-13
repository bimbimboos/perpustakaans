<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Member - Bimantara Pustaka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #38a169;
            --warning: #dd6b20;
            --danger: #e53e3e;
            --bg: #f5f7fa;
        }

        body {
            background: var(--bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            color: white;
            font-weight: 700;
            margin: 0;
        }

        .stats-row {
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid;
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .stat-card.total {
            border-color: var(--primary);
        }

        .stat-card.pending {
            border-color: var(--warning);
        }

        .stat-card.verified {
            border-color: var(--success);
        }

        .stat-card.rejected {
            border-color: var(--danger);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .stat-label {
            color: #718096;
            font-size: 0.875rem;
            margin: 0;
        }

        .filter-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }

        .table-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 1.5rem;
            color: white;
        }

        .table-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .btn-action {
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .member-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #718096;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* Modal Styles for Cool Effect */
        .modal-content {
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 700;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
            padding: 1rem 2rem;
        }

        .btn-close {
            filter: brightness(0) invert(1);
        }

        .form-label {
            font-weight: 600;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal.show .modal-dialog {
            animation: modalFadeIn 0.3s ease-out;
        }
    </style>
</head>
<body>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-users me-2"></i> Kelola Member Perpustakaan</h1>
        <p class="text-white-50 mb-0">Verifikasi dan kelola pendaftar member</p>
    </div>
</div>

<div class="container mb-5">

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>{{ session('success') }}</strong>
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

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show fade-in" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="stats-row row fade-in">
        <div class="col-md-3 mb-3">
            <div class="stat-card total">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-primary">{{ $members->total() }}</p>
                        <p class="stat-label">Total Member</p>
                    </div>
                    <div style="font-size: 3rem; color: var(--primary); opacity: 0.2;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card pending">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-warning">{{ \App\Models\Members::where('status', 'pending')->count() }}</p>
                        <p class="stat-label">Menunggu Verifikasi</p>
                    </div>
                    <div style="font-size: 3rem; color: var(--warning); opacity: 0.2;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card verified">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-success">{{ \App\Models\Members::where('status', 'verified')->count() }}</p>
                        <p class="stat-label">Terverifikasi</p>
                    </div>
                    <div style="font-size: 3rem; color: var(--success); opacity: 0.2;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card rejected">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-danger">{{ \App\Models\Members::where('status', 'rejected')->count() }}</p>
                        <p class="stat-label">Ditolak</p>
                    </div>
                    <div style="font-size: 3rem; color: var(--danger); opacity: 0.2;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="filter-card fade-in">
        <form method="GET" action="{{ route('members.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Cari Member</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nama, email, atau telepon..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                        ⏳ Pending
                    </option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>
                        ✅ Verified
                    </option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                        ❌ Rejected
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Verifikasi</label>
                <select name="verified" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" {{ request('verified') == '1' ? 'selected' : '' }}>Sudah</option>
                    <option value="0" {{ request('verified') == '0' ? 'selected' : '' }}>Belum</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">&nbsp;</label>
                <div class="d-grid gap-2 d-md-flex">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i> Filter
                    </button>
                    <a href="{{ route('members.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Members Table -->
    <div class="table-card fade-in">
        <div class="table-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5>
                    <i class="fas fa-list me-2"></i>
                    Daftar Member ({{ $members->total() }} member)
                </h5>
                <div>
                    @if(request('status') === 'pending')
                        <span class="badge bg-warning">
                            <i class="fas fa-filter me-1"></i>
                            Pending: {{ $members->total() }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th width="60">ID</th>
                    <th width="80">Foto</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Telp</th>
                    <th width="120">Status</th>
                    <th width="150">Tanggal Daftar</th>
                    <th width="220" class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($members as $member)
                    <tr>
                        <td>
                            <strong>#{{ str_pad($member->id_member, 4, '0', STR_PAD_LEFT) }}</strong>
                        </td>
                        <td>
                            @if($member->photo_path)
                                <img src="{{ route('members.download-photo', $member->id_member) }}"
                                     alt="{{ $member->name }}"
                                     class="member-photo">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&size=40&background=667eea&color=fff"
                                     alt="{{ $member->name }}"
                                     class="member-photo">
                            @endif
                        </td>
                        <td>
                            <strong>{{ $member->name }}</strong>
                        </td>
                        <td>
                            <i class="fas fa-envelope text-muted me-1"></i>
                            {{ $member->email }}
                        </td>
                        <td>
                            <i class="fas fa-phone text-muted me-1"></i>
                            {{ $member->no_telp }}
                        </td>
                        <td>
                            @if($member->status === 'pending')
                                <span class="badge bg-warning badge-status">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                            @elseif($member->status === 'verified')
                                <span class="badge bg-success badge-status">
                                        <i class="fas fa-check-circle me-1"></i> Verified
                                    </span>
                            @else
                                <span class="badge bg-danger badge-status">
                                        <i class="fas fa-times-circle me-1"></i> Rejected
                                    </span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $member->created_at->format('d M Y') }}
                                <br>
                                <i class="fas fa-clock me-1"></i>
                                {{ $member->created_at->format('H:i') }} WIB
                            </small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- View Detail -->
                                <a href="{{ route('members.show', $member->id_member) }}"
                                   class="btn btn-info btn-action"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Edit Button (Trigger Modal) -->
                                <button type="button"
                                        class="btn btn-primary btn-action"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $member->id_member }}"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                                @if($member->status === 'pending')
                                    <!-- ACC/Verifikasi (Kirim Email) -->
                                    <form action="{{ route('members.verify-manual', $member->id_member) }}"
                                          method="POST"
                                          style="display:inline;"
                                          onsubmit="return confirm('✅ Verifikasi member ini?\n\n📧 Sistem akan otomatis mengirim EMAIL berisi kode verifikasi ke:\n{{ $member->email }}')">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-success btn-action"
                                                title="ACC & Kirim Email">
                                            <i class="fas fa-check"></i> ACC
                                        </button>
                                    </form>

                                    <!-- Reject -->
                                    <form action="{{ route('members.reject', $member->id_member) }}"
                                          method="POST"
                                          style="display:inline;"
                                          onsubmit="return confirm('❌ Tolak pendaftar ini?')">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-warning btn-action"
                                                title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($member->status === 'verified' && $member->verification_code)
                                    <!-- Show Code -->
                                    <button type="button"
                                            class="btn btn-secondary btn-action"
                                            onclick="Swal.fire({
                                                    title: 'Kode Verifikasi',
                                                    html: '<div style=\'font-size:2rem;font-weight:700;letter-spacing:0.5rem\'>{{ $member->verification_code }}</div>',
                                                    icon: 'info'
                                                })"
                                            title="Lihat Kode">
                                        <i class="fas fa-key"></i>
                                    </button>
                                @endif

                                <!-- Delete Button (Trigger Modal, Admin Only) -->
                                @if(auth()->user()->role === 'admin')
                                    <button type="button"
                                            class="btn btn-danger btn-action"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $member->id_member }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <h5>Tidak Ada Data Member</h5>
                                <p class="text-muted">Belum ada pendaftar member</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($members->hasPages())
            <div class="p-3">
                {{ $members->links() }}
            </div>
        @endif
    </div>

    <!-- Back Button -->
    <div class="text-center mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Edit Modals (One per Member) -->
@foreach($members as $member)
    <div class="modal fade" id="editModal{{ $member->id_member }}" tabindex="-1" aria-labelledby="editModalLabel{{ $member->id_member }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel{{ $member->id_member }}"><i class="fas fa-edit me-2"></i> Edit Member: {{ $member->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('members.update', $member->id_member) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $member->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $member->email }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="no_telp" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="no_telp" name="no_telp" value="{{ $member->no_telp }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="pending" {{ $member->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="verified" {{ $member->status == 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="rejected" {{ $member->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="photo" class="form-label">Foto Baru (Opsional)</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Delete Modals (One per Member, Admin Only) -->
@foreach($members as $member)
    @if(auth()->user()->role === 'admin')
        <div class="modal fade" id="deleteModal{{ $member->id_member }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $member->id_member }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="deleteModalLabel{{ $member->id_member }}"><i class="fas fa-trash me-2"></i> Konfirmasi Hapus Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p class="fw-bold">Yakin ingin menghapus member "{{ $member->name }}"?</p>
                        <p class="text-muted">Aksi ini tidak bisa dibatalkan dan data akan hilang permanen!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Batal</button>
                        <form action="{{ route('members.destroy', $member->id_member) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i>Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // SweetAlert untuk success message
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        timer: 3000,
        showConfirmButton: false
    });
    @endif

    // SweetAlert untuk error message
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session("error") }}',
        timer: 3000,
        showConfirmButton: false
    });
    @endif
</script>
</body>
</html>
