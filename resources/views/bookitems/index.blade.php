@extends('layouts.app')

@section('content')
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Eksemplar</h5>
                @if(Auth::user()->role !== 'konsumen')
                    <a href="{{ route('books.items.create', $book->id_buku) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Tambah Eksemplar
                    </a>
                @endif
            </div>

            <div class="card-body">
                @if($items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                            <tr>
                                <th width="5%">#</th>
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
                                    <td><strong>{{ $item->id_item }}</strong></td>
                                    <td>
                                        @if($item->racks)
                                            <span class="badge bg-info">
                                            {{ $item->racks->kode_rak }}
                                        </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $item->racks->rackslocation->nama_lokasi ?? '-' }}
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

                                            // ✅ CEK APAKAH USER BISA PINJAM
                                            $canBorrow = $item->status === 'tersedia'
                                                      && $item->kondisi === 'baik';

                                            // ✅ CEK APAKAH MEMBER SUDAH VERIFIED
                                            $member = \App\Models\Members::where('id_user', Auth::id())->first();
                                            $isVerified = $member && $member->status === 'verified';
                                        @endphp

                                        @if($userRole === 'konsumen')
                                            @if(!$isVerified)
                                                <span class="text-warning small">
                                                <i class="fas fa-lock"></i> Akun belum diverifikasi
                                            </span>
                                            @elseif($canBorrow)
                                                <!-- ✅ TOMBOL PINJAM UNTUK KONSUMEN YANG VERIFIED -->
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
                                            <!-- ✅ ADMIN/PETUGAS: TOMBOL EDIT & HAPUS -->
                                            <a href="{{ route('books.items.edit', [$book->id_buku, $item->id_item]) }}"
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('books.items.destroy', [$book->id_buku, $item->id_item]) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Hapus eksemplar ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                <!-- ✅ MODAL PINJAM (UNTUK SETIAP ITEM) -->
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
                                                        <strong>Lokasi:</strong> {{ $item->racks->id_rak ?? '-' }}
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

                        @if(Auth::user()->role !== 'konsumen')
                            <a href="{{ route('books.items.create', $book->id_buku) }}" class="btn btn-sm btn-success ms-2">
                                <i class="fas fa-plus"></i> Tambah Eksemplar
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
