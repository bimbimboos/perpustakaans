@extends('layouts.app')

@section('content')
    <div class="container fade-in">
        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- TOMBOL DIPINDAH KE ATAS --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📚 Daftar Peminjaman Buku</h2>

            {{-- Tombol Tambah Peminjaman: hanya untuk admin/petugas --}}
            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'petugas']))
                <button class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPinjamBaru">
                    <i class="fas fa-plus-circle"></i> Tambah Peminjaman
                </button>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center">
                <thead class="table-dark">
                <tr>
                    <th>ID Transaksi</th>
                    <th>Nama Peminjam</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                @php
                    // Group borrowings by transaction_id (atau fallback ke user+date jika belum ada)
                    $groupedBorrowings = $borrowing->groupBy(function($item) {
                        return $item->transaction_id ?? ($item->id_user . '_' . \Carbon\Carbon::parse($item->pinjam)->format('Y-m-d'));
                    });
                @endphp

                @forelse($groupedBorrowings as $transactionId => $group)
                    @php
                        $firstItem = $group->first();
                        $bookCount = $group->count();
                        $allReturned = $group->every(fn($b) => in_array($b->status, ['Dikembalikan', 'dikembalikan']));
                        $borrowerName = $firstItem->users->name ?? ($firstItem->member->name ?? '-'); // PAKAI NAMA LAMA
                    @endphp
                    <tr>
                        <td>
                            <strong class="text-primary">
                                {{ $transactionId ?? 'TRX-OLD-' . $firstItem->id_peminjaman }}
                            </strong>
                        </td>
                        <td>
                            <strong>{{ $borrowerName }}</strong>
                        </td>
                        <td>
                            @php
                                $booksData = $group->map(function($b) {
                                    return [
                                        'id_peminjaman' => $b->id_peminjaman,
                                        'judul' => $b->books->judul ?? '-',
                                        'no_eksemplar' => $b->bookitems->id_item ?? '-',
                                        'rak' => $b->bookitems->racks->nama ?? '-',
                                        'status' => $b->status,
                                        'is_extended' => $b->is_extended,
                                        'pinjam' => \Carbon\Carbon::parse($b->pinjam)->format('d M Y'),
                                        'pengembalian' => \Carbon\Carbon::parse($b->pengembalian)->format('d M Y'),
                                        'kondisi' => $b->kondisi
                                    ];
                                })->values();
                            @endphp
                            <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalLihatBuku"
                                    onclick='showBooks(@json($booksData), "{{ $borrowerName }}", {{ $allReturned ? "true" : "false" }})'>
                                Lihat Buku ({{ $bookCount }})
                            </button>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($firstItem->pinjam)->format('d M Y') }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($firstItem->pengembalian)->format('d M Y') }}
                            @if(\Carbon\Carbon::parse($firstItem->pengembalian)->isPast() && in_array($firstItem->status, ['Dipinjam','dipinjam']))
                                <span class="badge bg-danger ms-1">Terlambat!</span>
                            @endif
                        </td>
                        <td>
            <span class="badge bg-{{ $firstItem->kondisi == 'baik' ? 'success' : 'warning' }}">
                {{ ucfirst($firstItem->kondisi) }}
            </span>
                        </td>
                        <td>
                            @if($allReturned)
                                <span class="badge bg-success">
                    Sudah Dikembalikan
                </span>
                            @else
                                @php
                                    $statusMap = [
                                        'pending' => ['badge' => 'warning', 'label' => 'Pending'],
                                        'Dipinjam' => ['badge' => 'primary', 'label' => 'Dipinjam'],
                                        'dipinjam' => ['badge' => 'primary', 'label' => 'Dipinjam'],
                                        'Dikembalikan' => ['badge' => 'success', 'label' => 'Dikembalikan'],
                                        'dikembalikan' => ['badge' => 'success', 'label' => 'Dikembalikan'],
                                        'ditolak' => ['badge' => 'danger', 'label' => 'Ditolak'],
                                    ];
                                    $status = $statusMap[$firstItem->status] ?? ['badge' => 'secondary', 'label' => ucfirst($firstItem->status)];
                                @endphp
                                <span class="badge bg-{{ $status['badge'] }}">{{ $status['label'] }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            Belum ada data peminjaman
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Lihat Buku --}}
    <div class="modal fade" id="modalLihatBuku" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-book"></i> Daftar Buku Dipinjam - <span id="borrowerName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="bookListContainer">
                        {{-- Will be filled by JavaScript --}}
                    </div>

                    {{-- Info Peminjaman --}}
                    <div class="card mt-3 bg-light">
                        <div class="card-body">
                            <h6 class="card-title fw-bold mb-3">📋 Informasi Peminjaman</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted">Tanggal Pinjam</small>
                                    <p class="mb-0 fw-semibold" id="borrowDate">-</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Batas Kembali</small>
                                    <p class="mb-0 fw-semibold" id="returnDate">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="modalFooter">
                    {{-- Buttons will be added by JavaScript if needed --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Kembalikan (Bulk) --}}
    <div class="modal fade" id="modalKembalikan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formKembalikan">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-undo"></i> Kembalikan Buku
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Anda akan mengembalikan <strong><span id="returnCount">0</span> buku</strong>.</p>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kondisi Buku <span class="text-danger">*</span></label>
                            <select name="kondisi" class="form-select" required>
                                <option value="baik">Baik</option>
                                <option value="rusak">Rusak</option>
                                <option value="hilang">Hilang</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Denda (Rp)</label>
                            <input type="number" name="denda" class="form-control" min="0" placeholder="0">
                            <small class="text-muted">Kosongkan jika tidak ada denda</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>

                        <input type="hidden" name="book_ids" id="returnBookIds">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Kembalikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Perpanjang (Bulk) --}}
    <div class="modal fade" id="modalPerpanjang" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formPerpanjang">
                    @csrf
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-plus"></i> Perpanjang Peminjaman
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Anda akan memperpanjang peminjaman <strong><span id="extendCount">0</span> buku</strong> selama 7 hari.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Peminjaman hanya bisa diperpanjang 1 kali.
                        </div>
                        <input type="hidden" name="book_ids" id="extendBookIds">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-calendar-check"></i> Perpanjang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== MODAL BARU: TAMBAH PEMINJAMAN DENGAN TOMBOL PILIH ===== --}}
    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'petugas']))
        <div class="modal fade" id="modalPinjamBaru" tabindex="-1" aria-labelledby="modalPinjamBaruLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form action="{{ route('borrowing.store') }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalPinjamBaruLabel">
                            <i class="fas fa-plus-circle"></i> Tambah Peminjaman Buku
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        {{-- FORM PEMINJAM --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="id_user" class="form-label fw-bold">
                                    <i class="fas fa-user"></i> Pilih Peminjam (Member Verified)
                                </label>
                                <select name="id_user" id="id_user" class="form-select" required>
                                    <option value="">-- Pilih Peminjam --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id_user }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="pengembalian" class="form-label fw-bold">
                                    <i class="fas fa-calendar"></i> Tanggal Pengembalian
                                </label>
                                <input
                                    type="date"
                                    name="pengembalian"
                                    id="pengembalian"
                                    class="form-control"
                                    min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                                    max="{{ \Carbon\Carbon::now()->addDays(14)->format('Y-m-d') }}"
                                    value="{{ \Carbon\Carbon::now()->addDays(7)->format('Y-m-d') }}"
                                    required
                                >
                                <small class="text-muted">Maksimal 14 hari dari sekarang</small>
                            </div>
                        </div>

                        <hr>

                        {{-- SEARCH & TABLE BUKU --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-search"></i> Cari & Pilih Buku untuk Dipinjam
                            </label>
                            <input
                                type="text"
                                id="searchBuku"
                                class="form-control form-control-lg mb-3"
                                placeholder="Ketik judul buku, pengarang, atau kategori..."
                            >

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Cara Pakai:</strong> Pilih 1 atau 2 eksemplar dari buku yang sama (tekan Ctrl/Cmd), atau pilih dari buku berbeda. Maksimal 2 eksemplar total per peminjaman.
                            </div>
                        </div>

                        {{-- List Buku Terpilih --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-shopping-cart"></i> Buku yang Dipilih (<span id="selectedCount">0</span>/2)
                            </label>
                            <div id="selectedBooksContainer" class="border rounded p-3 bg-light">
                                <p class="text-muted text-center mb-0">
                                    <i class="fas fa-info-circle"></i> Belum ada buku dipilih
                                </p>
                            </div>
                        </div>

                        {{-- TABLE DAFTAR BUKU --}}
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="table-dark sticky-top">
                                <tr class="text-center">
                                    <th width="40%">Judul Buku</th>
                                    <th width="20%">Pengarang</th>
                                    <th width="15%">Kategori</th>
                                    <th width="15%">Stok Tersedia</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                                </thead>
                                <tbody id="tableBukuBody">
                                @php
                                    // Group bookitems by id_buku untuk tampilan yang lebih rapi
                                    $booksWithItems = \App\Models\Books::with(['bookitems' => function($q) {
                                        $q->where('status', 'tersedia');
                                    }, 'categories', 'publisher'])
                                    ->whereHas('bookitems', function($q) {
                                        $q->where('status', 'tersedia');
                                    })
                                    ->get();
                                @endphp

                                @forelse($booksWithItems as $book)
                                    <tr class="book-row" data-judul="{{ strtolower($book->judul) }}"
                                        data-pengarang="{{ strtolower($book->pengarang ?? '') }}"
                                        data-kategori="{{ strtolower($book->categories->nama_kategori ?? '') }}">
                                        <td>
                                            <strong>{{ $book->judul }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $book->publisher->nama_penerbit ?? '-' }}</small>
                                        </td>
                                        <td>{{ $book->pengarang ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $book->categories->nama_kategori ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                {{ $book->bookitems->where('status', 'tersedia')->count() }} tersedia
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-primary pilih-eksemplar-btn"
                                                    data-book-id="{{ $book->id_buku }}"
                                                    data-book-title="{{ addslashes($book->judul) }}">
                                                <i class="fas fa-hand-pointer"></i> Pilih Eksemplar
                                            </button>

                                            {{-- Hidden div untuk store eksemplar data --}}
                                            <div class="d-none eksemplar-data" id="eksemplarData{{ $book->id_buku }}">
                                                @foreach($book->bookitems->where('status', 'tersedia') as $item)
                                                    <div class="eksemplar-item"
                                                         data-id="{{ $item->id_item }}"
                                                         data-kode="{{ $item->kode_item ?? $item->id_item }}"
                                                         data-rak="{{ $item->racks->nama ?? '-' }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                            Tidak ada buku dengan eksemplar tersedia
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Hidden inputs untuk eksemplar yang dipilih --}}
                        <div id="selectedItemsContainer"></div>

                        <div class="mt-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-clipboard-check"></i> Kondisi Buku Saat Dipinjam
                            </label>
                            <select name="kondisi" class="form-select">
                                <option value="baik" selected>Baik</option>
                                <option value="rusak">Rusak</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitPinjamBtn" disabled>
                            <i class="fas fa-save"></i> Simpan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ===== MODAL PILIH EKSEMPLAR (STEP 2) ===== --}}
    <div class="modal fade" id="modalPilihEksemplar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-books"></i> Pilih Eksemplar
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-book"></i> Buku Dipilih
                        </label>
                        <div class="alert alert-info mb-0">
                            <strong id="selectedBookTitle">-</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-check-square"></i> Pilih Eksemplar (Maksimal 2)
                        </label>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i>
                            <small>Sisa slot: <strong id="remainingSlots">2</strong></small>
                        </div>
                    </div>

                    <div class="list-group" id="eksemplarList">
                        <!-- Will be filled by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="button" class="btn btn-success" id="btnConfirmEksemplar" disabled>
                        <i class="fas fa-check-circle"></i> Konfirmasi Pilihan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-close alerts
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Initialize search functionality
            initializeBookSearch();

            // Initialize pilih eksemplar buttons
            initializePilihEksemplarButtons();
        });

        // ========================================
        // FITUR LIHAT BUKU (MODAL DETAIL)
        // ========================================
        let selectedBooks = [];
        let currentBooks = [];
        let isAllReturned = false;

        function showBooks(books, borrowerName, allReturned) {
            currentBooks = books;
            isAllReturned = allReturned;
            selectedBooks = [];

            document.getElementById('borrowerName').textContent = borrowerName;

            if (books.length === 0) {
                document.getElementById('bookListContainer').innerHTML = '<p class="text-muted">Tidak ada buku ditemukan</p>';
                return;
            }

            // Set dates from first book
            document.getElementById('borrowDate').textContent = books[0].pinjam || '-';
            document.getElementById('returnDate').textContent = books[0].pengembalian || '-';

            // Build book list HTML
            let html = '<div class="list-group">';

            books.forEach(book => {
                const isReturned = ['Dikembalikan', 'dikembalikan'].includes(book.status);
                const canExtend = !book.is_extended && !isReturned;

                html += `
                    <div class="list-group-item">
                        <div class="d-flex align-items-start gap-3">
                            ${!isReturned ? `
                                <input type="checkbox"
                                       class="form-check-input mt-1"
                                       value="${book.id_peminjaman}"
                                       onchange="toggleBook(${book.id_peminjaman})"
                                       style="width: 20px; height: 20px;">
                            ` : ''}
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">${book.judul || '-'}</h6>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-barcode"></i>
                                    ${book.no_eksemplar || '-'} — Rak: ${book.rak || '-'}
                                </p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge bg-${isReturned ? 'success' : 'warning'}">
                                        ${book.status}
                                    </span>
                                    ${book.is_extended ? '<span class="badge bg-info">Diperpanjang</span>' : ''}
                                    ${!canExtend && !isReturned ? '<span class="badge bg-secondary">Tidak bisa diperpanjang</span>' : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            document.getElementById('bookListContainer').innerHTML = html;

            // Update footer buttons
            updateFooter();
        }

        function toggleBook(bookId) {
            if (selectedBooks.includes(bookId)) {
                selectedBooks = selectedBooks.filter(id => id !== bookId);
            } else {
                selectedBooks.push(bookId);
            }
            updateFooter();
        }

        function updateFooter() {
            const footer = document.getElementById('modalFooter');

            if (isAllReturned) {
                footer.innerHTML = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';
                return;
            }

            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'petugas']))
                footer.innerHTML = `
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button"
                            class="btn btn-warning"
                            onclick="showExtendModal()"
                            ${selectedBooks.length === 0 ? 'disabled' : ''}>
                        <i class="fas fa-calendar-plus"></i> Perpanjang (${selectedBooks.length})
                    </button>
                    <button type="button"
                            class="btn btn-success"
                            onclick="showReturnModal()"
                            ${selectedBooks.length === 0 ? 'disabled' : ''}>
                        <i class="fas fa-check"></i> Kembalikan (${selectedBooks.length})
                    </button>
                `;
            @else
                footer.innerHTML = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';
            @endif
        }

        function showReturnModal() {
            if (selectedBooks.length === 0) return;

            document.getElementById('returnCount').textContent = selectedBooks.length;
            document.getElementById('returnBookIds').value = JSON.stringify(selectedBooks);

            const modal = new bootstrap.Modal(document.getElementById('modalKembalikan'));
            modal.show();
        }

        function showExtendModal() {
            if (selectedBooks.length === 0) return;

            document.getElementById('extendCount').textContent = selectedBooks.length;
            document.getElementById('extendBookIds').value = JSON.stringify(selectedBooks);

            const modal = new bootstrap.Modal(document.getElementById('modalPerpanjang'));
            modal.show();
        }

        // Form submissions
        document.getElementById('formKembalikan').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const bookIds = JSON.parse(formData.get('book_ids'));

            // Submit each book return
            Promise.all(bookIds.map(id => {
                return fetch(`/borrowing/${id}/kembali`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        kondisi: formData.get('kondisi'),
                        denda: formData.get('denda'),
                        catatan: formData.get('catatan')
                    })
                });
            }))
                .then(() => {
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengembalikan buku');
                });
        });

        document.getElementById('formPerpanjang').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const bookIds = JSON.parse(formData.get('book_ids'));

            // Submit each book extension
            Promise.all(bookIds.map(id => {
                return fetch(`/borrowing/${id}/perpanjang`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            }))
                .then(() => {
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memperpanjang peminjaman');
                });
        });

        // ========================================
        // FITUR TAMBAH PEMINJAMAN (REDESIGNED WITH MODAL)
        // ========================================

        // Array untuk menyimpan buku yang dipilih
        let selectedBooksData = [];
        let currentBookId = null;
        let currentBookTitle = null;
        let currentEksemplars = [];
        let tempSelectedEksemplars = [];

        // Search functionality untuk table buku
        function initializeBookSearch() {
            const searchInput = document.getElementById('searchBuku');
            if (!searchInput) return;

            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.book-row');

                rows.forEach(row => {
                    const judul = row.getAttribute('data-judul') || '';
                    const pengarang = row.getAttribute('data-pengarang') || '';
                    const kategori = row.getAttribute('data-kategori') || '';

                    const searchText = judul + ' ' + pengarang + ' ' + kategori;

                    if (searchText.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Initialize pilih eksemplar buttons with event delegation
        function initializePilihEksemplarButtons() {
            console.log('Initializing pilih eksemplar buttons...');

            // Use event delegation on table body instead of individual buttons
            const tableBody = document.getElementById('tableBukuBody');
            if (!tableBody) {
                console.error('Table body not found');
                return;
            }

            // Remove old listener if exists
            tableBody.removeEventListener('click', handlePilihEksemplarClick);

            // Add new listener
            tableBody.addEventListener('click', handlePilihEksemplarClick);

            console.log('Event delegation setup complete');
        }

        // Handle click on pilih eksemplar buttons
        function handlePilihEksemplarClick(e) {
            const button = e.target.closest('.pilih-eksemplar-btn');
            if (!button) return;

            e.preventDefault();
            e.stopPropagation();

            console.log('Button clicked!');

            const bookId = button.dataset.bookId;
            const bookTitle = button.dataset.bookTitle;

            console.log('Book ID:', bookId, 'Title:', bookTitle);

            // Get eksemplar data from hidden div
            const eksemplarContainer = document.getElementById(`eksemplarData${bookId}`);

            if (!eksemplarContainer) {
                console.error('Eksemplar container not found for book:', bookId);
                return;
            }

            const eksemplarItems = eksemplarContainer.querySelectorAll('.eksemplar-item');
            console.log('Found eksemplar items:', eksemplarItems.length);

            const eksemplars = Array.from(eksemplarItems).map(item => ({
                id: item.dataset.id,
                kode: item.dataset.kode,
                rak: item.dataset.rak
            }));

            console.log('Eksemplars data:', eksemplars);

            openEksemplarModal(bookId, bookTitle, eksemplars);
        }

        // Open modal untuk pilih eksemplar
        function openEksemplarModal(bookId, bookTitle, eksemplars) {
            console.log('Opening modal for:', bookId, bookTitle, eksemplars);
            currentBookId = bookId;
            currentBookTitle = bookTitle;
            currentEksemplars = eksemplars;
            tempSelectedEksemplars = [];

            // Set book title
            document.getElementById('selectedBookTitle').textContent = bookTitle;

            // Calculate remaining slots
            const remainingSlots = 2 - selectedBooksData.length;
            document.getElementById('remainingSlots').textContent = remainingSlots;

            // Build eksemplar list
            const listContainer = document.getElementById('eksemplarList');
            listContainer.innerHTML = '';

            // Filter out already selected items
            const availableEksemplars = eksemplars.filter(ex =>
                !selectedBooksData.some(b => b.itemId === ex.id)
            );

            if (availableEksemplars.length === 0) {
                listContainer.innerHTML = `
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        Semua eksemplar buku ini sudah dipilih!
                    </div>
                `;
            } else {
                availableEksemplars.forEach(ex => {
                    const item = document.createElement('label');
                    item.className = 'list-group-item list-group-item-action d-flex align-items-center';
                    item.style.cursor = 'pointer';
                    item.innerHTML = `
                        <input type="checkbox"
                               class="form-check-input me-3 eksemplar-checkbox"
                               value="${ex.id}"
                               data-kode="${ex.kode}"
                               data-rak="${ex.rak}"
                               onchange="toggleEksemplarCheckbox()"
                               style="width: 20px; height: 20px;">
                        <div class="flex-grow-1">
                            <div class="fw-bold">${ex.kode}</div>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> Rak: ${ex.rak}
                            </small>
                        </div>
                    `;
                    listContainer.appendChild(item);
                });
            }

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('modalPilihEksemplar'));
            modal.show();

            // Reset confirm button
            document.getElementById('btnConfirmEksemplar').disabled = true;
        }

        // Toggle checkbox eksemplar
        function toggleEksemplarCheckbox() {
            const checkboxes = document.querySelectorAll('.eksemplar-checkbox:checked');
            const remainingSlots = 2 - selectedBooksData.length;
            const confirmBtn = document.getElementById('btnConfirmEksemplar');

            // Limit selection to remaining slots
            if (checkboxes.length > remainingSlots) {
                // Uncheck the last one
                checkboxes[checkboxes.length - 1].checked = false;

                // Show sweet alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-2';
                alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Maksimal ${remainingSlots} eksemplar!</strong> Anda sudah memilih ${selectedBooksData.length} sebelumnya.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                const existingAlert = document.querySelector('#modalPilihEksemplar .alert-danger');
                if (existingAlert) existingAlert.remove();

                document.querySelector('#eksemplarList').insertAdjacentElement('beforebegin', alertDiv);

                setTimeout(() => alertDiv.remove(), 3000);
                return;
            }

            // Update temp selection
            tempSelectedEksemplars = Array.from(checkboxes).map(cb => ({
                id: cb.value,
                kode: cb.dataset.kode,
                rak: cb.dataset.rak
            }));

            // Enable/disable confirm button
            confirmBtn.disabled = tempSelectedEksemplars.length === 0;
        }

        // Confirm eksemplar selection
        document.getElementById('btnConfirmEksemplar')?.addEventListener('click', function() {
            if (tempSelectedEksemplars.length === 0) return;

            // Add to selected books
            tempSelectedEksemplars.forEach(ex => {
                selectedBooksData.push({
                    bookId: currentBookId,
                    itemId: ex.id,
                    judul: currentBookTitle,
                    eksemplar: `${ex.kode} - Rak: ${ex.rak}`
                });
            });

            // Update UI
            updateSelectedBooksUI();

            // Close modal properly
            const modalElement = document.getElementById('modalPilihEksemplar');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }

            // Reset temp
            tempSelectedEksemplars = [];

            // Clean up modal backdrop
            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }, 300);
        });

        // Fungsi untuk menghapus buku dari pilihan
        function hapusBuku(index) {
            // Remove from array
            selectedBooksData.splice(index, 1);

            // Update UI
            updateSelectedBooksUI();
        }

        // Update tampilan buku terpilih
        function updateSelectedBooksUI() {
            const container = document.getElementById('selectedBooksContainer');
            const hiddenContainer = document.getElementById('selectedItemsContainer');
            const counter = document.getElementById('selectedCount');
            const submitBtn = document.getElementById('submitPinjamBtn');

            // Update counter
            counter.textContent = selectedBooksData.length;

            // Update hidden inputs
            hiddenContainer.innerHTML = '';
            selectedBooksData.forEach(book => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id_item[]';
                input.value = book.itemId;
                hiddenContainer.appendChild(input);
            });

            // Update submit button
            submitBtn.disabled = selectedBooksData.length === 0;

            // Update display
            if (selectedBooksData.length === 0) {
                container.innerHTML = `
                    <p class="text-muted text-center mb-0">
                        <i class="fas fa-info-circle"></i> Belum ada buku dipilih
                    </p>
                `;
            } else {
                let html = '<div class="list-group">';
                selectedBooksData.forEach((book, index) => {
                    html += `
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <strong>${book.judul}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-barcode"></i> ${book.eksemplar}
                                </small>
                            </div>
                            <button type="button"
                                    class="btn btn-sm btn-danger ms-2"
                                    onclick="hapusBuku(${index})"
                                    title="Hapus">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        }

        // Reset when modal is closed
        document.getElementById('modalPinjamBaru')?.addEventListener('hidden.bs.modal', function() {
            selectedBooksData = [];
            updateSelectedBooksUI();
        });

        // Clean up modal eksemplar when closed
        document.getElementById('modalPilihEksemplar')?.addEventListener('hidden.bs.modal', function() {
            // Clear any alerts
            const alerts = this.querySelectorAll('.alert-danger');
            alerts.forEach(alert => alert.remove());

            // Reset checkboxes
            const checkboxes = this.querySelectorAll('.eksemplar-checkbox');
            checkboxes.forEach(cb => cb.checked = false);

            // Reset temp selection
            tempSelectedEksemplars = [];

            // Clean backdrop just in case
            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }, 100);
        });

        // Re-initialize buttons when modal tambah peminjaman is shown
        document.getElementById('modalPinjamBaru')?.addEventListener('shown.bs.modal', function() {
            console.log('Modal Tambah Peminjaman shown, re-initializing buttons...');
            initializePilihEksemplarButtons();
        });
    </script>
@endpush

@push('styles')
    <style>
        .list-group-item {
            border: 1px solid var(--card-border);
            background-color: var(--card-bg);
            transition: background-color 0.2s;
            color: var(--body-color);
        }

        .list-group-item:hover {
            background-color: var(--table-header-bg);
        }

        .list-group-item h6 {
            color: var(--body-color);
        }

        .list-group-item .text-muted {
            opacity: 0.7;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modal styling for dark mode */
        .modal-content {
            background-color: var(--card-bg);
            color: var(--body-color);
            border: 1px solid var(--card-border);
        }

        .modal-header {
            border-bottom: 1px solid var(--card-border);
        }

        .modal-footer {
            border-top: 1px solid var(--card-border);
        }

        .modal-body {
            color: var(--body-color);
        }

        .card.bg-light {
            background-color: var(--table-header-bg) !important;
            border: 1px solid var(--card-border);
        }

        .card.bg-light .card-body {
            color: var(--body-color);
        }

        .card.bg-light .text-muted {
            opacity: 0.7;
        }

        /* Form controls in modal */
        .modal .form-control,
        .modal .form-select {
            background-color: var(--card-bg);
            color: var(--body-color);
            border: 1px solid var(--card-border);
        }

        .modal .form-control:focus,
        .modal .form-select:focus {
            background-color: var(--card-bg);
            color: var(--body-color);
            border-color: #0d6efd;
        }

        .modal .form-control::placeholder {
            color: var(--body-color);
            opacity: 0.5;
        }

        /* Alert in modal */
        .modal .alert-info {
            background-color: rgba(13, 110, 253, 0.1);
            border-color: rgba(13, 110, 253, 0.3);
            color: var(--body-color);
        }

        /* Selected books container */
        #selectedBooksContainer {
            min-height: 60px;
            background-color: var(--table-header-bg) !important;
        }

        #selectedBooksContainer .list-group-item {
            margin-bottom: 0.5rem;
        }

        #selectedBooksContainer .list-group-item:last-child {
            margin-bottom: 0;
        }

        /* Button states */
        .pilih-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-success.pilih-btn {
            pointer-events: none;
        }

        /* Eksemplar modal styling */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        #modalPilihEksemplar .list-group-item {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        #modalPilihEksemplar .list-group-item:hover {
            background-color: var(--table-header-bg);
            border-left-color: #667eea;
            transform: translateX(5px);
        }

        #modalPilihEksemplar .list-group-item:has(input:checked) {
            background-color: rgba(102, 126, 234, 0.1);
            border-left-color: #667eea;
        }

        #modalPilihEksemplar .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        /* Alert styling in modal */
        #modalPilihEksemplar .alert {
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush
