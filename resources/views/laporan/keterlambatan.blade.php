@extends('layouts.app')

@section('content')
    <div class="container fade-in">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-exclamation-triangle"></i> Daftar Keterlambatan</h2>
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
                                <h6 class="text-muted mb-1">Terlambat Ringan (1-3 hari)</h6>
                                <h3 class="mb-0 text-warning">{{ $terlambatRingan }}</h3>
                            </div>
                            <div class="text-warning" style="font-size: 2.5rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-orange shadow-sm" style="border-color: #fd7e14 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Terlambat Sedang (4-7 hari)</h6>
                                <h3 class="mb-0" style="color: #fd7e14;">{{ $terlambatSedang }}</h3>
                            </div>
                            <div style="font-size: 2.5rem; color: #fd7e14;">
                                <i class="fas fa-exclamation-circle"></i>
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
                                <h6 class="text-muted mb-1">Terlambat Berat (>7 hari)</h6>
                                <h3 class="mb-0 text-danger">{{ $terlambatBerat }}</h3>
                            </div>
                            <div class="text-danger" style="font-size: 2.5rem;">
                                <i class="fas fa-ban"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert Peringatan --}}
        @if($borrowings->count() > 0)
            <div class="alert alert-danger shadow-sm" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Peringatan Keterlambatan!
                </h5>
                <p class="mb-0">
                    Terdapat <strong>{{ $borrowings->count() }} peminjaman</strong> yang terlambat dikembalikan.
                    Segera hubungi peminjam untuk pengembalian dan pengenaan sanksi sesuai aturan.
                </p>
            </div>
        @endif

        {{-- Table --}}
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Detail Keterlambatan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="tableKeterlambatan">
                        <thead class="table-dark">
                        <tr>
                            <th>Prioritas</th>
                            <th>ID Transaksi</th>
                            <th>Peminjam</th>
                            <th>Kontak</th>
                            <th>Buku</th>
                            <th>Batas Kembali</th>
                            <th>Terlambat</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($borrowings as $b)
                            @php
                                $daysLate = $b->getDaysLate();
                                $priority = $daysLate > 7 ? 'danger' : ($daysLate > 3 ? 'warning' : 'info');
                                $priorityIcon = $daysLate > 7 ? 'ban' : ($daysLate > 3 ? 'exclamation-triangle' : 'clock');
                            @endphp
                            <tr class="table-{{ $priority }} bg-opacity-10">
                                <td class="text-center">
                                    <span class="badge bg-{{ $priority }} fs-6">
                                        <i class="fas fa-{{ $priorityIcon }}"></i>
                                    </span>
                                </td>
                                <td><strong class="text-primary">{{ $b->id_peminjaman }}</strong></td>
                                <td>
                                    <strong>{{ $b->users->name ?? $b->member->name ?? '-' }}</strong>
                                </td>
                                <td>
                                    @if($b->member)
                                        <i class="fas fa-phone"></i> {{ $b->member->no_telp ?? '-' }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $b->books->judul ?? '-' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Eks: {{ $b->bookitems->id_item ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($b->pengembalian)->format('d M Y') }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($b->pengembalian)->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $priority }} fs-6">
                                        {{ $daysLate }} hari
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('borrowing.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                                    <p class="mb-0">Tidak ada keterlambatan! Semua peminjaman tepat waktu.</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#tableKeterlambatan').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                    },
                    order: [[6, 'desc']] // Sort by hari terlambat terbanyak
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

            .border-orange {
                border-width: 1px !important;
                border-style: solid !important;
            }
        </style>
    @endpush
@endsection
