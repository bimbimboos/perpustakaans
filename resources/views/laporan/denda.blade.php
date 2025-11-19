@extends('layouts.app')

@section('content')
    <div class="container fade-in">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-money-bill-wave"></i> Daftar Denda</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-danger shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Denda</h6>
                                <h3 class="mb-0 text-danger">Rp {{ number_format($totalDenda, 0, ',', '.') }}</h3>
                            </div>
                            <div class="text-danger" style="font-size: 2.5rem;">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Belum Dibayar</h6>
                                <h3 class="mb-0 text-warning">{{ $dendaBelumBayar }}</h3>
                            </div>
                            <div class="text-warning" style="font-size: 2.5rem;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Transaksi</h6>
                                <h3 class="mb-0 text-info">{{ $borrowings->count() }}</h3>
                            </div>
                            <div class="text-info" style="font-size: 2.5rem;">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Detail Denda</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="tableDenda">
                        <thead class="table-dark">
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Terlambat</th>
                            <th>Denda</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($borrowings as $b)
                            <tr>
                                <td><strong class="text-primary">{{ $b->id_peminjaman }}</strong></td>
                                <td>{{ $b->users->name ?? $b->member->name ?? '-' }}</td>
                                <td>{{ $b->books->judul ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($b->pinjam)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($b->pengembalian)->format('d M Y') }}</td>
                                <td>
                                    @if($b->isLate())
                                        <span class="badge bg-danger">{{ $b->getDaysLate() }} hari</span>
                                    @else
                                        <span class="badge bg-success">Tepat waktu</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-danger">Rp {{ number_format($b->denda ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if(in_array($b->status, ['Dikembalikan', 'dikembalikan']))
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @else
                                        <span class="badge bg-warning">Dipinjam</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Tidak ada data denda
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
                $('#tableDenda').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                    },
                    order: [[4, 'asc']] // Sort by batas kembali
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
