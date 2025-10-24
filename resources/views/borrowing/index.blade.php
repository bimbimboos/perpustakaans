@extends('layouts.app')

@section('content')
    <div class="container fade-in">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>📚 Daftar Peminjaman Buku</h2>

            {{-- Tombol Tambah Peminjaman --}}
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPinjam">
                <i class="fas fa-plus"></i> Tambah Peminjaman
            </button>
        </div>

        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
            <tr>
                <th>Nama Peminjam</th>
                <th>Buku</th>
                <th>Eksemplar</th>
                <th>Tgl Pinjam</th>
                <th>Batas Kembali</th>
                <th>Kondisi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            </thead>

            <tbody>
            @forelse($borrowing as $b)
                <tr>
                    <td>{{ $b->users->name }}</td>
                    <td>{{ $b->books->judul }}</td>
                    <td>{{ $b->bookitems->kode_item }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->pinjam)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->pengembalian)->format('d M Y') }}</td>
                    <td>{{ ucfirst($b->kondisi) }}</td>
                    <td>
                        @php
                            $badge = [
                                'pending' => 'warning',
                                'dipinjam' => 'primary',
                                'kembali' => 'success',
                                'ditolak' => 'danger',
                                'diperpanjang' => 'info',
                            ][$b->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $badge }}">{{ ucfirst($b->status) }}</span>
                    </td>

                    <td>
                        {{-- Tindakan berdasarkan status --}}
                        @if($b->status === 'pending')
                            <form action="{{ route('borrowing.approve', $b->id_peminjaman) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm"><i class="fas fa-check"></i> Setujui</button>
                            </form>

                            <form action="{{ route('borrowing.reject', $b->id_peminjaman) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak</button>
                            </form>

                        @elseif($b->status === 'dipinjam')
                            <form action="{{ route('borrowing.update', $b->id_peminjaman) }}" method="POST" class="d-inline">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-warning btn-sm">
                                    <i class="fas fa-clock"></i> Perpanjang
                                </button>
                            </form>

                            <form action="{{ route('borrowing.kembalikan', $b->id_peminjaman) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm">
                                    <i class="fas fa-undo"></i> Kembalikan
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted">Belum ada data peminjaman</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah Peminjaman --}}
    <div class="modal fade" id="modalPinjam" tabindex="-1" aria-labelledby="modalPinjamLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('borrowing.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalPinjamLabel">Tambah Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_user" class="form-label">Peminjam</label>
                        <select name="id_user" id="id_user" class="form-select" required>
                            @foreach($users as $u)
                                <option value="{{ $u->id_user }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_item" class="form-label">Eksemplar Buku</label>
                        <select name="id_item" id="id_item" class="form-select" required>
                            @foreach($bookitems as $i)
                                <option value="{{ $i->id_item }}">{{ $i->books->judul }} — {{ $i->kode_item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="pengembalian" class="form-label">Tanggal Pengembalian</label>
                        <input type="date" name="pengembalian" id="pengembalian" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="kondisi" class="form-label">Kondisi Buku</label>
                        <select name="kondisi" id="kondisi" class="form-select">
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                            <option value="hilang">Hilang</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
