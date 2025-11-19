@extends('layouts.app')

@section('content')
    <div class="container fade-in">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-tools"></i> Daftar Buku Rusak & Hilang</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-warning shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Rusak</h6>
                                <h3 class="mb-0 text-warning">{{ $totalRusak }}</h3>
                            </div>
                            <div class="text-warning" style="font-size: 2.5rem;">
                                <i class="fas fa-wrench"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Hilang</h6>
                                <h3 class="mb-0 text-danger">{{ $totalHilang }}</h3>
                            </div>
                            <div class="text-danger" style="font-size: 2.5rem;">
                                <i class="fas fa-question-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-dark shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Kerugian</h6>
                                <h3 class="mb-0 text-dark">Rp {{ number_format($totalKerugian, 0, ',', '.') }}</h3>
                            </div>
                            <div class="text-dark" style="font-size: 2.5rem;">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-list"></i> Detail Buku Rusak & Hilang</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="tableBukuRusak">
                        <thead class="table-dark">
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Eksemplar</th>
                            <th>Kondisi</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Denda</th>
                            <th>Catatan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($borrowings as $b)
                            <tr>
                                <td><strong class="text-primary">{{ $b->id_peminjaman }}</strong></td>
                                <td>
                                    <strong>{{ $b->users->name ?? $b->member->name ?? '-' }}</strong>
                                    <br>
                                    @if($b->member)
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> {{ $b->member->no_telp ?? '-' }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $b->books->judul ?? '-' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $b->books->pengarang ?? '-' }}</small>
                                </td>
                                <td>
                                    <code>{{ $b->bookitems->id_item ?? '-' }}</code>
                                    <br>
                                    <small class="text-muted">Rak: {{ $b->bookitems->racks->nama ?? '-' }}</small>
                                </td>
                                <td>
                                    @if($b->kondisi === 'rusak')
                                        <span class="badge bg-warning fs-6">
                                            <i class="fas fa-wrench"></i> Rusak
                                        </span>
                                    @elseif($b->kondisi === 'hilang')
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-question-circle"></i> Hilang
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($b->kondisi) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($b->pinjam)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($b->pengembalian)->format('d M Y') }}</td>
                                <td>
                                    @if($b->denda > 0)
                                        <strong class="text-danger">Rp {{ number_format($b->denda, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">Rp 0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($b->catatan)
                                        <button class="btn btn-sm btn-info"
                                                data-bs-toggle="tooltip"
                                                title="{{ $b->catatan }}">
                                            <i class="fas fa-sticky-note"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                                    <p class="mb-0">Tidak ada buku rusak atau hilang. Semua buku dalam kondisi baik!</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Info Panel --}}
        @if($borrowings->count() > 0)
            <div class="card mt-4 border-info shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi & Tindak Lanjut</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Buku Rusak:</strong> Evaluasi tingkat kerusakan dan tentukan apakah bisa diperbaiki atau harus diganti.</li>
                        <li><strong>Buku Hilang:</strong> Hubungi peminjam untuk penggantian buku atau pembayaran sesuai harga buku.</li>
                        <li><strong>Dokumentasi:</strong> Catat semua detail kerusakan/kehilangan untuk arsip perpustakaan.</li>
                        <li><strong>Sanksi:</strong> Terapkan sanksi sesuai peraturan perpustakaan yang berlaku.</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#tableBukuRusak').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                    },
                    order: [[5, 'desc']] // Sort by tanggal pinjam terbaru
                });

                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            });
        </script>
    @endpush

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        <style>
            .fade-in {
                animation: fadeIn 0.5s ease-in;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    @endpush
@endsection
