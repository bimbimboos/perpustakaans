@extends('layouts.app')

@section('content')
    <style>
        /* Fix dropdown di dalam modal */
        .modal {
            overflow-y: auto !important;
        }
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        select.form-select {
            position: relative;
            z-index: 1050;
        }
    </style>

    <div class="container-fluid">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">📚 Daftar Eksemplar Buku</h3>
                        <p class="mb-0">
                            <strong>{{ $book->judul }}</strong> •
                            {{ $book->pengarang }} •
                            {{ $book->publisher->nama_penerbit ?? '-' }} •
                            {{ $book->tahun_terbit }}
                        </p>
                    </div>
                    <a href="{{ route('books.show', $book->id_buku) }}" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistik Ringkas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Total Eksemplar</h6>
                        <h2>{{ $items->total() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Tersedia</h6>
                        <h2>{{ $items->where('status', 'tersedia')->count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h6>Dipinjam</h6>
                        <h2>{{ $items->where('status', 'dipinjam')->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Eksemplar -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Eksemplar</h5>
            </div>

            <div class="card-body">
                @if($items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                            <tr>
                                <th width="5%">Id Item</th>
                                <th width="15%">Kode Item</th>
                                <th width="15%">Lokasi Rak</th>
                                <th width="12%">Status</th>
                                <th width="12%">Kondisi</th>
                                <th width="15%">Sumber</th>
                                <th width="26%">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($items as $index => $item)
                                <tr>
                                    <td>{{ $items->firstItem() + $index }}</td>
                                    <td><strong>{{ $item->barcode }}</strong></td>
                                    <td>
                                        {{-- DEBUG: {{ $item->id_rak }} - {{ $item->racks ? 'Ada' : 'Kosong' }} --}}
                                        @if($item->racks)
                                            <span class="badge bg-info">
                                                {{ $item->racks->nama }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $item->racks->rackslocation->id_lokasi ?? '-' }}
                                            </small>
                                        @else
                                            <span class="text-muted">Belum ditata</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status === 'tersedia')
                                            <span class="badge bg-success">Tersedia</span>
                                        @elseif($item->status === 'dipinjam')
                                            <span class="badge bg-danger">Dipinjam</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->kondisi === 'baik')
                                            <span class="badge bg-success">Baik</span>
                                        @elseif($item->kondisi === 'rusak')
                                            <span class="badge bg-warning text-dark">Rusak</span>
                                        @elseif($item->kondisi === 'hilang')
                                            <span class="badge bg-danger">Hilang</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $item->sumber ?? '-' }}</small></td>
                                    <td>
                                        @php
                                            $userRole = Auth::user()->role;
                                            $canBorrow = $item->status === 'tersedia' && $item->kondisi === 'baik';
                                            $member = \App\Models\Members::where('id_member', Auth::id())->first();
                                            $isVerified = $member && $member->status === 'verified';
                                        @endphp

                                        @if($userRole === 'konsumen')
                                            @if(!$isVerified)
                                                <span class="text-warning small">
                                                    <i class="fas fa-lock"></i> Akun belum diverifikasi
                                                </span>
                                            @elseif($canBorrow)
                                                <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalPinjam{{ $item->id_item }}">
                                                    <i class="fas fa-hand-holding"></i> Pinjam
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    @if($item->status === 'dipinjam')
                                                        Sedang Dipinjam
                                                    @elseif($item->kondisi !== 'baik')
                                                        Kondisi {{ ucfirst($item->kondisi) }}
                                                    @else
                                                        Tidak Tersedia
                                                    @endif
                                                </button>
                                            @endif
                                        @else
                                            <!-- ✅ TOMBOL EDIT & HAPUS UNTUK ADMIN/PETUGAS -->
                                            <button class="btn btn-sm btn-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEdit{{ $item->id_item }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalHapus{{ $item->id_item }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- ✅ MODAL PINJAM (UNTUK KONSUMEN) -->
                                @if($userRole === 'konsumen')
                                    <div class="modal fade" id="modalPinjam{{ $item->id_item }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">Pinjam Buku</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('borrowing.borrow') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id_buku" value="{{ $book->id_buku }}">
                                                    <input type="hidden" name="id_item" value="{{ $item->id_item }}">

                                                    <div class="modal-body">
                                                        <div class="alert alert-info">
                                                            <strong>Buku:</strong> {{ $book->judul }}<br>
                                                            <strong>Item:</strong> {{ $item->id_item }}<br>
                                                            <strong>Lokasi:</strong> {{ $item->racks->barcode }}
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal Pengembalian <span class="text-danger">*</span></label>
                                                            <input type="date"
                                                                   name="pengembalian"
                                                                   class="form-control"
                                                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                                                   max="{{ date('Y-m-d', strtotime('+14 days')) }}"
                                                                   required>
                                                            <small class="text-muted">Maksimal 14 hari dari hari ini</small>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-check"></i> Pinjam
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- ✅ MODAL EDIT (UNTUK ADMIN/PETUGAS) -->
                                @if($userRole === 'admin' || $userRole === 'petugas')
                                    <div class="modal fade" id="modalEdit{{ $item->id_item }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title">Edit Eksemplar</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('books.items.update', [$book->id_buku, $item->id_item]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="tersedia" {{ $item->status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                                                <option value="dipinjam" {{ $item->status === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Kondisi</label>
                                                            <select name="kondisi" class="form-select" required>
                                                                <option value="baik" {{ $item->kondisi === 'baik' ? 'selected' : '' }}>Baik</option>
                                                                <option value="rusak" {{ $item->kondisi === 'rusak' ? 'selected' : '' }}>Rusak</option>
                                                                <option value="hilang" {{ $item->kondisi === 'hilang' ? 'selected' : '' }}>Hilang</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Lokasi Rak</label>
                                                            <select name="id_rak" class="form-select">
                                                                <option value="">-- Pilih Rak --</option>
                                                                @foreach($racks as $rack)
                                                                    <option value="{{ $rack->id_rak }}"
                                                                        {{ $item->id_rak == $rack->id_rak ? 'selected' : '' }}>
                                                                        {{ $rack->kode_rak }}
                                                                        @if($rack->rackslocation)
                                                                            - {{ $rack->rackslocation->nama_lokasi }}
                                                                        @endif
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <small class="text-muted">Kosongkan jika belum ditata</small>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Sumber</label>
                                                            <input type="text" name="sumber" class="form-control" value="{{ $item->sumber }}">
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning">
                                                            <i class="fas fa-save"></i> Simpan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ✅ MODAL HAPUS (UNTUK ADMIN/PETUGAS) -->
                                    <div class="modal fade" id="modalHapus{{ $item->id_item }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('books.items.destroy', [$book->id_buku, $item->id_item]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')

                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            <strong>Perhatian!</strong>
                                                        </div>
                                                        <p>Apakah Anda yakin ingin menghapus eksemplar ini?</p>
                                                        <ul>
                                                            <li><strong>Kode Item:</strong> {{ $item->id_item }}</li>
                                                            <li><strong>Status:</strong> {{ ucfirst($item->status) }}</li>
                                                            <li><strong>Kondisi:</strong> {{ ucfirst($item->kondisi) }}</li>
                                                        </ul>
                                                        <p class="text-danger"><strong>Data yang dihapus tidak dapat dikembalikan!</strong></p>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash"></i> Ya, Hapus
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $items->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Belum ada eksemplar untuk buku ini.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
