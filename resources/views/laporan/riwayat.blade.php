@extends('layouts.app')

@section('content')
    <div class="container fade-in">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-history"></i> Riwayat Transaksi (7 Hari Terakhir)</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Transaksi</h6>
                                <h3 class="mb-0 text-primary">{{ $totalTransaksi }}</h3>
                            </div>
                            <div class="text-primary" style="font-size: 2.5rem;">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Buku</h6>
                                <h3 class="mb-0 text-info">{{ $totalBuku }}</h3>
                            </div>
                            <div class="text-info" style="font-size: 2.5rem;">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Dikembalikan</h6>
                                <h3 class="mb-0 text-success">{{ $totalDikembalikan }}</h3>
                            </div>
                            <div class="text-success" style="font-size: 2.5rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Dipinjam</h6>
                                <h3 class="mb-0 text-warning">{{ $totalDipinjam }}</h3>
                            </div>
                            <div class="text-warning" style="font-size: 2.5rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Detail Riwayat</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="tableRiwayat">
                        <thead class="table-dark">
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Eksemplar</th>
                            <th>Tgl Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($borrowings as $b)
                            <tr>
                                <td><strong class="text-primary">{{ $b->id_peminjaman }}</strong></td>
                                <td>{{ $b->users->name ?? $b->member->name ?? '-' }}</td>
                                <td>{{ $b->books->judul ?? '-' }}</td>
                                <td>{{ $b->bookitems->id_item ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($b->pinjam)->format('d M Y H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($b->pengembalian)->format('d M Y') }}</td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'Dipinjam' => ['class' => 'warning', 'icon' => 'clock'],
                                            'dipinjam' => ['class' => 'warning', 'icon' => 'clock'],
                                            'Dikembalikan' => ['class' => 'success', 'icon' => 'check-circle'],
                                            'dikembalikan' => ['class' => 'success', 'icon' => 'check-circle'],
                                        ];
                                        $status = $statusMap[$b->status] ?? ['class' => 'secondary', 'icon' => 'info-circle'];
                                    @endphp
                                    <span class="badge bg-{{ $status['class'] }}">
                                        <i class="fas fa-{{ $status['icon'] }}"></i> {{ ucfirst($b->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($b->denda > 0)
                                        <strong class="text-danger">Rp {{ number_format($b->denda, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Tidak ada riwayat transaksi 7 hari terakhir
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
                $('#tableRiwayat').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                    },
                    order: [[4, 'desc']] // Sort by tanggal pinjam terbaru
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
