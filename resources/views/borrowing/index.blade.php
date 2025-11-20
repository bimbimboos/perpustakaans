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

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📚 Daftar Peminjaman Buku</h2>
            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'petugas']))
                <button class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPinjamBaru">
                    <i class="fas fa-plus-circle"></i> Tambah Peminjaman
                </button>
            @endif
        </div>

        {{-- TABLE PEMINJAMAN --}}
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
                    $groupedBorrowings = $borrowing->groupBy(function($item) {
                        return $item->transaction_id ?? ($item->id_member . '_' . \Carbon\Carbon::parse($item->pinjam)->format('Y-m-d'));
                    });
                @endphp
                @forelse($groupedBorrowings as $transactionId => $group)
                    @php
                        $firstItem = $group->first();
                        $bookCount = $group->count();
                        $allReturned = $group->every(fn($b) => in_array($b->status, ['Dikembalikan', 'dikembalikan']));
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
                        $borrowerNameClean = $firstItem->users->name ?? ($firstItem->member->name ?? '-');
                    @endphp
                    <tr>
                        <td><strong class="text-primary">{{ $firstItem->transaction_id ?? 'TRX-' . $firstItem->id_member . '-' . \Carbon\Carbon::parse($firstItem->pinjam)->format('Ymd') }}</strong></td>
                        <td><strong>{{ $borrowerNameClean }}</strong></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalLihatBuku"
                                    onclick='showBooks(@json($booksData), "{{ $borrowerNameClean }}", {{ $allReturned ? 'true' : 'false' }})'>
                                <i class="fas fa-book"></i> Lihat Buku ({{ $bookCount }})
                            </button>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($firstItem->pinjam)->format('d M Y') }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($firstItem->pengembalian)->format('d M Y') }}
                            @if(\Carbon\Carbon::parse($firstItem->pengembalian)->isPast() && in_array($firstItem->status, ['Dipinjam','dipinjam']))
                                <span class="badge bg-danger ms-1">Terlambat!</span>
                            @endif
                        </td>
                        <td><span class="badge bg-{{ $firstItem->kondisi == 'baik' ? 'success' : 'warning' }}">{{ ucfirst($firstItem->kondisi) }}</span></td>
                        <td>
                            @if($allReturned)
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Sudah Dikembalikan</span>
                            @else
                                @if($firstItem->is_extended)
                                    <span class="badge bg-info"><i class="fas fa-clock"></i> Diperpanjang</span>
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
                    <h5 class="modal-title"><i class="fas fa-book"></i> Daftar Buku Dipinjam - <span id="borrowerName"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="bookListContainer"></div>
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
                <div class="modal-footer" id="modalFooter"></div>
            </div>
        </div>
    </div>

    {{-- Modal Perpanjang --}}
    <div class="modal fade" id="modalPerpanjang" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form id="formPerpanjang">
                    @csrf
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="fas fa-calendar-plus"></i> Perpanjang Peminjaman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Peminjaman akan diperpanjang <strong>7 hari</strong> dari tanggal pengembalian saat ini.
                            <br>Perpanjangan hanya bisa dilakukan <strong>1 kali</strong>.
                        </div>
                        <h6 class="fw-bold mb-3">📚 Buku yang akan diperpanjang:</h6>
                        <div id="extendBookList" class="list-group mb-3"></div>
                        <input type="hidden" name="book_ids" id="extendBookIds">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-calendar-check"></i> Perpanjang (<span id="extendCountFooter">0</span> Buku)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Kembalikan --}}
    <div class="modal fade" id="modalKembalikan" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form id="formKembalikan">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fas fa-undo"></i> Kembalikan Buku</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian:</strong> Pastikan semua kondisi buku sudah benar sebelum menyimpan!
                        </div>
                        <h6 class="fw-bold mb-3">📚 Buku yang akan dikembalikan:</h6>
                        <div id="returnBookList"></div>
                        <div class="card mt-3 bg-light">
                            <div class="card-body">
                                <h6 class="card-title fw-bold">💰 Denda & Catatan</h6>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Total Denda (Rp)</label>
                                    <input type="number" name="denda" id="totalDenda" class="form-control" min="0" value="0" placeholder="0">
                                    <small class="text-muted">Kosongkan atau isi 0 jika tidak ada denda</small>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-bold">Catatan Tambahan</label>
                                    <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan umum untuk pengembalian ini (opsional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Kembalikan (<span id="returnCountFooter">0</span> Buku)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH PEMINJAMAN --}}
    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'petugas']))
        <div class="modal fade" id="modalPinjamBaru" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <form action="{{ route('borrowing.store') }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Peminjaman Buku</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- FORM PEMINJAM --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fas fa-user"></i> Peminjam (Member Verified)</label>
                                <div class="input-group">
                                    <input type="text" id="selectedMemberName" class="form-control" placeholder="-- Pilih Peminjam --" readonly required>
                                    <input type="hidden" name="id_member" id="id_member" required>
                                    <button type="button" class="btn btn-primary" onclick="openMemberModal()">
                                        <i class="fas fa-search"></i> Pilih
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i> Member yang sudah meminjam 2 buku tidak bisa dipilih
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label for="pengembalian" class="form-label fw-bold"><i class="fas fa-calendar"></i> Tanggal Pengembalian</label>
                                <input type="date" name="pengembalian" id="pengembalian" class="form-control"
                                       min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                                       max="{{ \Carbon\Carbon::now()->addDays(14)->format('Y-m-d') }}"
                                       value="{{ \Carbon\Carbon::now()->addDays(7)->format('Y-m-d') }}" required>
                                <small class="text-muted">Maksimal 14 hari dari sekarang</small>
                            </div>
                        </div>

                        {{-- MEMBER INFO DISPLAY --}}
                        <div class="row mb-3">
                            <div class="col-12"><div id="memberBorrowInfo"></div></div>
                        </div>

                        <hr>

                        {{-- SEARCH & TABLE BUKU --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-search"></i> Cari & Pilih Buku untuk Dipinjam</label>
                            <input type="text" id="searchBuku" class="form-control form-control-lg mb-3"
                                   placeholder="Ketik judul buku, pengarang, atau kategori...">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Cara Pakai:</strong> Centang buku, lalu klik "Lihat Eksemplar" untuk memilih 1 eksemplar.
                                Per buku hanya bisa pilih 1 eksemplar. Maksimal 2 buku per member.
                            </div>
                        </div>

                        {{-- TABLE DAFTAR BUKU --}}
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="table-dark sticky-top">
                                <tr class="text-center">
                                    <th width="5%"><input type="checkbox" id="selectAllBooks" class="form-check-input"></th>
                                    <th width="30%">Judul Buku</th>
                                    <th width="20%">Pengarang</th>
                                    <th width="15%">Kategori</th>
                                    <th width="10%">Stok</th>
                                    <th width="20%">Eksemplar</th>
                                </tr>
                                </thead>
                                <tbody id="tableBukuBody">
                                @php
                                    $booksWithItems = \App\Models\Books::with(['bookitems' => function($q) {
                                        $q->where('status', 'tersedia')->where('kondisi', 'baik');
                                    }, 'categories', 'publisher'])
                                    ->whereHas('bookitems', function($q) {
                                        $q->where('status', 'tersedia')->where('kondisi', 'baik');
                                    })
                                    ->get();
                                @endphp
                                @forelse($booksWithItems as $book)
                                    <tr class="book-row" data-judul="{{ strtolower($book->judul) }}"
                                        data-pengarang="{{ strtolower($book->pengarang ?? '') }}"
                                        data-kategori="{{ strtolower($book->categories->nama_kategori ?? '') }}">
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input book-checkbox"
                                                   data-book-id="{{ $book->id_buku }}"
                                                   onchange="toggleEksemplarSelect({{ $book->id_buku }})">
                                        </td>
                                        <td>
                                            <strong>{{ $book->judul }}</strong><br>
                                            <small class="text-muted">{{ $book->publisher->nama_penerbit ?? '-' }}</small>
                                        </td>
                                        <td>{{ $book->pengarang ?? '-' }}</td>
                                        <td><span class="badge bg-secondary">{{ $book->categories->nama_kategori ?? '-' }}</span></td>
                                        <td class="text-center">
                                    <span class="badge bg-success">
                                        {{ $book->bookitems->where('status', 'tersedia')->where('kondisi', 'baik')->count() }}
                                    </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="event.preventDefault(); openModalEksemplar({{ $book->id_buku }})"
                                                    id="btnEksemplar{{ $book->id_buku }}" disabled>
                                                <i class="fas fa-list-check"></i> Lihat Eksemplar
                                            </button>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <span id="selectedCount{{ $book->id_buku }}">0</span> eksemplar dipilih
                                                </small>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                            Tidak ada buku dengan eksemplar tersedia (kondisi baik)
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Hidden inputs & Kondisi (auto baik) --}}
                        <div id="selectedItemsContainer"></div>
                        <input type="hidden" name="kondisi" value="baik">
                    </div>

                    <div class="modal-footer">
                        <div class="me-auto">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Buku yang dipilih: <strong id="selectedCount">0</strong> / <strong id="maxCount">2</strong>
                            </small>
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitPeminjaman">
                            <i class="fas fa-save"></i> Simpan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Pilih Eksemplar (Radio Button) --}}
        @php
            $booksForModal = \App\Models\Books::with(['bookitems' => function($q) {
                $q->where('status', 'tersedia')->where('kondisi', 'baik');
            }, 'categories', 'publisher'])
            ->whereHas('bookitems', function($q) {
                $q->where('status', 'tersedia')->where('kondisi', 'baik');
            })
            ->get();
        @endphp
        @foreach($booksForModal as $book)
            <div class="modal fade" id="modalEksemplar{{ $book->id_buku }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-list-check"></i> Pilih Eksemplar - {{ $book->judul }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Pilih 1 eksemplar buku yang akan dipinjam.</strong>
                                <br><small>⚠️ Per buku hanya bisa meminjam 1 eksemplar. Hanya eksemplar dengan kondisi BAIK yang ditampilkan.</small>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control search-eksemplar"
                                       placeholder="Cari berdasarkan kode eksemplar atau rak..."
                                       onkeyup="searchEksemplar({{ $book->id_buku }}, this)">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="table-dark">
                                    <tr>
                                        <th width="10%">Pilih</th>
                                        <th>Kode Eksemplar</th>
                                        <th>Rak</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody id="eksemplarBody{{ $book->id_buku }}">
                                    @forelse($book->bookitems->where('status', 'tersedia')->where('kondisi', 'baik') as $item)
                                        <tr class="eksemplar-row-{{ $book->id_buku }}"
                                            data-kode="{{ strtolower($item->kode_item ?? $item->id_item) }}"
                                            data-rak="{{ strtolower($item->racks->nama ?? '') }}">
                                            <td class="text-center">
                                                <input type="radio" class="form-check-input eksemplar-radio-{{ $book->id_buku }}"
                                                       name="eksemplar_{{ $book->id_buku }}" value="{{ $item->id_item }}"
                                                       data-book-id="{{ $book->id_buku }}"
                                                       onchange="updateModalCountRadio({{ $book->id_buku }})">
                                            </td>
                                            <td><strong>{{ $item->kode_item ?? $item->id_item }}</strong></td>
                                            <td>{{ $item->racks->nama ?? '-' }}</td>
                                            <td><span class="badge bg-success">Tersedia</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada eksemplar tersedia (kondisi baik)</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-check-square"></i>
                                    Eksemplar dipilih: <strong id="modalCount{{ $book->id_buku }}">0</strong>
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary"
                                    onclick="confirmSelection({{ $book->id_buku }})"
                                    data-bs-dismiss="modal">
                                <i class="fas fa-check"></i> Konfirmasi Pilihan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Modal Pilih Member --}}
        <div class="modal fade" id="modalPilihMember" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-users"></i> Pilih Member Verified</h5>
                        <button type="button" class="btn-close btn-close-white" onclick="closeMemberModalOnly()"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="searchMember" class="form-control mb-3"
                               placeholder="🔍 Cari nama member..." onkeyup="filterAndPaginateMembers()">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark sticky-top">
                                <tr>
                                    <th width="10%">Pilih</th>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Status Peminjaman</th>
                                </tr>
                                </thead>
                                <tbody id="memberTableBody">
                                @foreach($members as $member)
                                    @php
                                        $activeBorrowCount = \App\Models\Borrowing::where('id_member', $member->id_member)
                                            ->whereIn('status', ['Dipinjam', 'dipinjam', 'pending'])->count();
                                        $isMaxed = $activeBorrowCount >= 2;
                                        $memberType = $member->id_user ? 'Online' : 'Walk-in';
                                    @endphp
                                    <tr class="member-row" data-nama="{{ strtolower($member->name) }}">
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm {{ $isMaxed ? 'btn-secondary' : 'btn-primary' }}"
                                                    onclick="selectMember({{ $member->id_member }}, '{{ addslashes($member->name) }}', {{ $activeBorrowCount }})"
                                                {{ $isMaxed ? 'disabled' : '' }}>
                                                {{ $isMaxed ? '❌' : 'Pilih' }}
                                            </button>
                                        </td>
                                        <td>
                                            <strong>{{ $member->name }}</strong>
                                            <br><small class="text-muted">{{ $member->email }}</small>
                                        </td>
                                        <td>
                                            @if($member->id_user)
                                                <span class="badge bg-info">🌐 Online</span>
                                            @else
                                                <span class="badge bg-secondary">🏢 Walk-in</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isMaxed)
                                                <span class="badge bg-danger">❌ Limit ({{ $activeBorrowCount }}/2)</span>
                                            @elseif($activeBorrowCount === 1)
                                                <span class="badge bg-warning">⚠️ {{ $activeBorrowCount }}/2 Dipinjam</span>
                                            @else
                                                <span class="badge bg-success">✅ {{ $activeBorrowCount }}/2 Dipinjam</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    Menampilkan <strong id="memberShowingStart">0</strong>-<strong id="memberShowingEnd">0</strong>
                                    dari <strong id="memberTotal">0</strong> member
                                </small>
                            </div>
                            <nav><ul class="pagination pagination-sm mb-0" id="memberPagination"></ul></nav>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeMemberModalOnly()">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // ========================================
        // INITIALIZATION
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
            initializeBookSearch();
        });

        // Global variables
        window.maxBooksAllowed = 2;
        let selectedBooks = [];
        let currentBooks = [];
        let isAllReturned = false;
        let memberModal = null;
        let currentMemberPage = 1;
        const membersPerPage = 10;
        let filteredMembers = [];

        // ========================================
        // MEMBER SELECTION
        // ========================================
        function openMemberModal() {
            const modalEl = document.getElementById('modalPilihMember');
            if (!modalEl) return;

            if (!memberModal) {
                memberModal = new bootstrap.Modal(modalEl, {
                    backdrop: 'static',
                    keyboard: false
                });
            }

            const searchInput = document.getElementById('searchMember');
            if (searchInput) searchInput.value = '';

            memberModal.show();

            // ✅ PERBAIKAN: Panggil SETELAH modal shown
            setTimeout(() => {
                filterAndPaginateMembers();
            }, 100);
        }

        function closeMemberModalOnly() {
            if (memberModal) memberModal.hide();
        }

        function selectMember(memberId, memberName, borrowCount) {
            console.log('✅ selectMember called:', memberId, memberName, borrowCount);

            const memberIdInput = document.getElementById('id_member');
            const memberNameInput = document.getElementById('selectedMemberName');

            if (!memberIdInput || !memberNameInput) {
                console.error('❌ Input elements not found!');
                return;
            }

            memberIdInput.value = memberId;
            memberNameInput.value = memberName;

            updateMemberInfoDisplay(memberName, borrowCount);
            closeMemberModalOnly();
        }

        function selectMemberAndClose(userId, userName, borrowCount) {
            selectMember(userId, userName, borrowCount);
        }

        function updateMemberInfoDisplay(memberName, borrowCount) {
            const infoContainer = document.getElementById('memberBorrowInfo');
            const submitBtn = document.getElementById('btnSubmitPeminjaman');
            const maxCountElement = document.getElementById('maxCount');

            if (!infoContainer) return;

            const warningEl = document.getElementById('limitWarning');
            if (warningEl) warningEl.remove();

            if (borrowCount >= 2) {
                infoContainer.innerHTML = `
            <div class="alert alert-danger">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-times-circle fa-2x"></i>
                    <div>
                        <strong>${memberName}</strong> sudah mencapai limit peminjaman!
                        <br><small>Member ini sedang meminjam <strong>2/2 buku aktif</strong>.</small>
                    </div>
                </div>
            </div>
        `;
                if (submitBtn) submitBtn.disabled = true;

                document.querySelectorAll('.book-checkbox:checked').forEach(cb => {
                    cb.checked = false;
                    const bookId = cb.getAttribute('data-book-id');
                    if (bookId) toggleEksemplarSelect(parseInt(bookId));
                });
                return;
            }

            if (submitBtn) submitBtn.disabled = false;
            const remainingSlots = 2 - borrowCount;
            const percentage = (borrowCount / 2) * 100;

            if (maxCountElement) maxCountElement.textContent = remainingSlots;
            window.maxBooksAllowed = remainingSlots;

            let progressColor = borrowCount === 1 ? 'warning' : 'success';
            let alertColor = borrowCount === 1 ? 'warning' : 'info';
            let icon = borrowCount === 1 ? 'exclamation-triangle' : 'info-circle';

            infoContainer.innerHTML = `
        <div class="alert alert-${alertColor} mb-0">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-${icon}"></i>
                <strong>Status Peminjaman: ${memberName}</strong>
            </div>
            <div class="mb-2">
                <small class="d-block mb-1">
                    Sedang Meminjam: <strong>${borrowCount}/2 buku</strong> •
                    Sisa Slot: <strong>${remainingSlots} buku</strong>
                </small>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-${progressColor}"
                         role="progressbar" style="width: ${percentage}%">
                    </div>
                </div>
            </div>
            <small>
                ${borrowCount === 1
                ? '⚠️ Member ini hanya bisa meminjam <strong>maksimal 1 buku lagi</strong>.'
                : '✅ Member ini bisa meminjam <strong>maksimal 2 buku</strong>.'}
            </small>
        </div>
    `;

            updateMainCount();
        }

        // ========================================
        // PAGINATION MODAL MEMBER
        // ========================================
        function filterAndPaginateMembers() {
            const searchInput = document.getElementById('searchMember');
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            const allRows = document.querySelectorAll('.member-row');

            filteredMembers = Array.from(allRows).filter(row => {
                const nama = row.getAttribute('data-nama') || '';
                return nama.includes(searchTerm);
            });

            const totalEl = document.getElementById('memberTotal');
            if (totalEl) totalEl.textContent = filteredMembers.length;

            currentMemberPage = 1;
            displayMemberPage(1);
        }

        function displayMemberPage(page) {
            const start = (page - 1) * membersPerPage;
            const end = start + membersPerPage;

            document.querySelectorAll('.member-row').forEach(row => row.style.display = 'none');
            filteredMembers.slice(start, end).forEach(row => row.style.display = '');

            const startEl = document.getElementById('memberShowingStart');
            const endEl = document.getElementById('memberShowingEnd');

            if (startEl) startEl.textContent = filteredMembers.length > 0 ? start + 1 : 0;
            if (endEl) endEl.textContent = Math.min(end, filteredMembers.length);

            renderMemberPagination();
        }

        function renderMemberPagination() {
            const totalPages = Math.ceil(filteredMembers.length / membersPerPage);
            const container = document.getElementById('memberPagination');
            if (!container) return;

            let html = `
        <li class="page-item ${currentMemberPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changeMemberPage(${currentMemberPage - 1}); return false;">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentMemberPage - 1 && i <= currentMemberPage + 1)) {
                    html += `
                <li class="page-item ${i === currentMemberPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changeMemberPage(${i}); return false;">${i}</a>
                </li>
            `;
                } else if (i === currentMemberPage - 2 || i === currentMemberPage + 2) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            html += `
        <li class="page-item ${currentMemberPage >= totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changeMemberPage(${currentMemberPage + 1}); return false;">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;

            container.innerHTML = html;
        }

        function changeMemberPage(page) {
            const totalPages = Math.ceil(filteredMembers.length / membersPerPage);
            if (page < 1 || page > totalPages) return;

            currentMemberPage = page;
            displayMemberPage(page);
        }

        // ========================================
        // LIHAT BUKU MODAL
        // ========================================
        function showBooks(books, borrowerName, allReturned) {
            currentBooks = books;
            isAllReturned = allReturned;
            selectedBooks = [];

            document.getElementById('borrowerName').textContent = borrowerName;

            if (books.length === 0) {
                document.getElementById('bookListContainer').innerHTML = '<p class="text-muted">Tidak ada buku ditemukan</p>';
                return;
            }

            document.getElementById('borrowDate').textContent = books[0].pinjam || '-';
            document.getElementById('returnDate').textContent = books[0].pengembalian || '-';

            let html = '<div class="list-group">';
            books.forEach(book => {
                const isReturned = ['Dikembalikan', 'dikembalikan'].includes(book.status);

                html += `
            <div class="list-group-item">
                <div class="d-flex align-items-start gap-3">
                    ${!isReturned ? `
                        <input type="checkbox" class="form-check-input mt-1"
                               value="${book.id_peminjaman}"
                               onchange="toggleBook(${book.id_peminjaman})"
                               style="width: 20px; height: 20px;">
                    ` : ''}
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">${book.judul || '-'}</h6>
                        <p class="mb-1 text-muted small">
                            <i class="fas fa-barcode"></i> ${book.no_eksemplar || '-'} — Rak: ${book.rak || '-'}
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-${isReturned ? 'success' : 'warning'}">${book.status}</span>
                            ${isReturned ? `
                                <span class="badge bg-${book.kondisi === 'baik' ? 'success' : book.kondisi === 'rusak' ? 'warning' : 'danger'}">
                                    ${book.kondisi === 'baik' ? '✅' : book.kondisi === 'rusak' ? '⚠️' : '❌'}
                                    Kondisi: ${book.kondisi.charAt(0).toUpperCase() + book.kondisi.slice(1)}
                                </span>
                            ` : ''}
                            ${book.is_extended ? '<span class="badge bg-info">Diperpanjang</span>' : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
            });
            html += '</div>';
            document.getElementById('bookListContainer').innerHTML = html;
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
            <button type="button" class="btn btn-warning" onclick="showExtendModal()"
                    ${selectedBooks.length === 0 ? 'disabled' : ''}>
                <i class="fas fa-calendar-plus"></i> Perpanjang (${selectedBooks.length})
            </button>
            <button type="button" class="btn btn-success" onclick="showReturnModal()"
                    ${selectedBooks.length === 0 ? 'disabled' : ''}>
                <i class="fas fa-check"></i> Kembalikan (${selectedBooks.length})
            </button>
        `;
            @else
                footer.innerHTML = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';
            @endif
        }

        // ========================================
        // PERPANJANG & KEMBALIKAN
        // ========================================
        function showExtendModal() {
            if (selectedBooks.length === 0) return;
            const booksToExtend = currentBooks.filter(book => selectedBooks.includes(book.id_peminjaman));

            document.getElementById('extendCountFooter').textContent = booksToExtend.length;
            document.getElementById('extendBookIds').value = JSON.stringify(selectedBooks);

            let html = '';
            booksToExtend.forEach((book, index) => {
                const newReturnDate = new Date(book.pengembalian);
                newReturnDate.setDate(newReturnDate.getDate() + 7);
                const formattedNewDate = newReturnDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

                html += `
            <div class="list-group-item">
                <div class="d-flex align-items-start gap-3">
                    <div class="badge bg-primary fs-6" style="min-width: 30px;">${index + 1}</div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">${book.judul}</h6>
                        <p class="mb-1 text-muted small">
                            <i class="fas fa-barcode"></i> ${book.no_eksemplar} • <i class="fas fa-warehouse"></i> Rak: ${book.rak}
                        </p>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-secondary">Saat ini: ${book.pengembalian}</span>
                            <i class="fas fa-arrow-right"></i>
                            <span class="badge bg-success">Baru: ${formattedNewDate}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
            });

            document.getElementById('extendBookList').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('modalPerpanjang'));
            modal.show();
        }

        function showReturnModal() {
            if (selectedBooks.length === 0) return;
            const booksToReturn = currentBooks.filter(book => selectedBooks.includes(book.id_peminjaman));

            document.getElementById('returnCountFooter').textContent = booksToReturn.length;

            let html = '';
            booksToReturn.forEach((book, index) => {
                html += `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="badge bg-primary fs-6" style="min-width: 30px;">${index + 1}</div>
                        <div class="flex-grow-1">
                            <h6 class="mb-2 fw-bold">${book.judul}</h6>
                            <p class="mb-2 text-muted small">
                                <i class="fas fa-barcode"></i> ${book.no_eksemplar} • <i class="fas fa-warehouse"></i> Rak: ${book.rak}
                            </p>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small mb-1">Kondisi Buku <span class="text-danger">*</span></label>
                                    <select name="kondisi_${book.id_peminjaman}" class="form-select form-select-sm" required>
                                        <option value="baik">✅ Baik</option>
                                        <option value="rusak">⚠️ Rusak</option>
                                        <option value="hilang">❌ Hilang</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small mb-1">Denda Buku Ini (Rp)</label>
                                    <input type="number" name="denda_${book.id_peminjaman}"
                                           class="form-control form-control-sm denda-input"
                                           min="0" value="0" placeholder="0" onchange="calculateTotalDenda()">
                                </div>
                            </div>
                            <input type="hidden" name="book_ids[]" value="${book.id_peminjaman}">
                        </div>
                    </div>
                </div>
            </div>
        `;
            });

            document.getElementById('returnBookList').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('modalKembalikan'));
            modal.show();
        }

        function calculateTotalDenda() {
            let total = 0;
            document.querySelectorAll('.denda-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('totalDenda').value = total;
        }

        // ========================================
        // FORM SUBMISSIONS
        // ========================================
        document.getElementById('formKembalikan')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const bookIds = formData.getAll('book_ids[]');

            Promise.all(bookIds.map(id => {
                return fetch(`/borrowing/${id}/kembali`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        kondisi: formData.get(`kondisi_${id}`),
                        denda: formData.get(`denda_${id}`) || 0,
                        catatan: formData.get('catatan'),
                        total_denda: formData.get('denda') || 0
                    })
                });
            })).then(() => window.location.reload())
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengembalikan buku');
                });
        });

        document.getElementById('formPerpanjang')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const bookIds = JSON.parse(formData.get('book_ids'));

            Promise.all(bookIds.map(id => {
                return fetch(`/borrowing/${id}/perpanjang`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            })).then(() => window.location.reload())
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memperpanjang peminjaman');
                });
        });

        // ========================================
        // TAMBAH PEMINJAMAN - BOOK SELECTION
        // ========================================
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

                    row.style.display = searchText.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        function openModalEksemplar(bookId) {
            const modalElement = document.getElementById(`modalEksemplar${bookId}`);
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }

        function toggleEksemplarSelect(bookId) {
            const checkbox = document.querySelector(`.book-checkbox[data-book-id="${bookId}"]`);
            const button = document.getElementById(`btnEksemplar${bookId}`);

            if (checkbox.checked) {
                button.disabled = false;
            } else {
                button.disabled = true;
                const container = document.getElementById('selectedItemsContainer');
                container.querySelectorAll(`input[data-book-id="${bookId}"]`).forEach(input => input.remove());

                document.getElementById(`selectedCount${bookId}`).textContent = '0';
                document.querySelectorAll(`.eksemplar-radio-${bookId}`).forEach(radio => radio.checked = false);

                const modalCount = document.getElementById(`modalCount${bookId}`);
                if (modalCount) modalCount.textContent = '0';
            }

            updateMainCount();
        }

        function updateModalCountRadio(bookId) {
            const checked = document.querySelector(`.eksemplar-radio-${bookId}:checked`);
            document.getElementById(`modalCount${bookId}`).textContent = checked ? '1' : '0';
        }

        function searchEksemplar(bookId, input) {
            const searchTerm = input.value.toLowerCase();
            document.querySelectorAll(`.eksemplar-row-${bookId}`).forEach(row => {
                const kode = row.getAttribute('data-kode') || '';
                const rak = row.getAttribute('data-rak') || '';
                const searchText = kode + ' ' + rak;

                row.style.display = searchText.includes(searchTerm) ? '' : 'none';
            });
        }

        function getExistingCountExcept(bookId) {
            let count = 0;
            document.querySelectorAll('#selectedItemsContainer input[name="id_item[]"]').forEach(input => {
                if (parseInt(input.getAttribute('data-book-id')) !== bookId) {
                    count++;
                }
            });
            return count;
        }

        function confirmSelection(bookId) {
            const selectedRadio = document.querySelector(`.eksemplar-radio-${bookId}:checked`);

            if (!selectedRadio) {
                alert('❌ Pilih 1 eksemplar!');
                return false;
            }

            const selectedId = selectedRadio.value;
            const existingCount = getExistingCountExcept(bookId);
            const maxAllowed = window.maxBooksAllowed || 2;

            if (existingCount >= maxAllowed) {
                alert(`❌ Limit tercapai! Maksimal ${maxAllowed} buku.`);
                return false;
            }

            document.getElementById(`selectedCount${bookId}`).textContent = '1';

            const container = document.getElementById('selectedItemsContainer');
            container.querySelectorAll(`input[data-book-id="${bookId}"]`).forEach(input => input.remove());

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id_item[]';
            input.value = selectedId;
            input.setAttribute('data-book-id', bookId);
            container.appendChild(input);

            updateMainCount();
        }

        function updateMainCount() {
            const count = document.querySelectorAll('#selectedItemsContainer input[name="id_item[]"]').length;
            document.getElementById('selectedCount').textContent = count;

            const maxAllowed = window.maxBooksAllowed || 2;

            document.querySelectorAll('.book-checkbox').forEach(cb => {
                if (!cb.checked && count >= maxAllowed) {
                    cb.disabled = true;
                } else if (!cb.checked && count < maxAllowed) {
                    cb.disabled = false;
                }
            });

            const warningEl = document.getElementById('limitWarning');
            if (count >= maxAllowed) {
                if (!warningEl) {
                    const counterContainer = document.getElementById('selectedCount').parentElement;
                    counterContainer.insertAdjacentHTML('afterend', `
                <div id="limitWarning" class="text-warning small">
                    <i class="fas fa-exclamation-triangle"></i> Limit tercapai!
                </div>
            `);
                }
            } else {
                if (warningEl) warningEl.remove();
            }
        }

        document.getElementById('selectAllBooks')?.addEventListener('change', function() {
            const visibleCheckboxes = Array.from(document.querySelectorAll('.book-checkbox')).filter(cb => {
                return cb.closest('tr').style.display !== 'none';
            });

            visibleCheckboxes.slice(0, 2).forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = this.checked;
                    const bookId = checkbox.getAttribute('data-book-id');
                    toggleEksemplarSelect(bookId);
                }
            });
        });

        // Form validation
        document.querySelector('#modalPinjamBaru form')?.addEventListener('submit', function(e) {
            const count = document.querySelectorAll('#selectedItemsContainer input[name="id_item[]"]').length;
            const maxAllowed = window.maxBooksAllowed || 2;
            if (count > maxAllowed || count === 0) {
                e.preventDefault();
                alert(`❌ Peminjaman gagal! Total buku harus antara 1-${maxAllowed}. Saat ini: ${count}`);
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .list-group-item {
            border: 1px solid var(--card-border);
            background-color: var(--card-bg);
            transition: all 0.2s;
            color: var(--body-color);
        }

        .list-group-item:hover {
            background-color: var(--table-header-bg);
            transform: translateX(3px);
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

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

        .card.bg-light {
            background-color: var(--table-header-bg) !important;
            border: 1px solid var(--card-border);
        }

        .card {
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

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

        .modal .alert-info {
            background-color: rgba(13, 110, 253, 0.1);
            border-color: rgba(13, 110, 253, 0.3);
            color: var(--body-color);
        }

        .table-responsive {
            border: 1px solid var(--card-border);
            border-radius: 0.375rem;
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .progress {
            background-color: rgba(0,0,0,0.1);
            border-radius: 5px;
        }

        .progress-bar {
            transition: width 0.3s ease;
        }

        .alert {
            border-left: 4px solid currentColor;
            animation: slideIn 0.3s ease;
        }

        .alert-info {
            border-left-color: #0dcaf0;
        }

        .alert-warning {
            border-left-color: #ffc107;
        }

        .alert-danger {
            border-left-color: #dc3545;
        }
    </style>
@endpush
