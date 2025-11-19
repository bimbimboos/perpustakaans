@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Daftar Buku</h1>
        @unless(Auth::user()->role === 'konsumen')
            <button class="btn btn-primary shadow-sm px-3 py-2 fw-bold hover-scale"
                    data-bs-toggle="modal" data-bs-target="#modalTambahBuku">
                + Tambah Buku
            </button>
        @endunless
    </div>

    <!--search-->
    <form method="GET" action="{{ route('books.index') }}" class="mb-3">
        <div class="input-group" style="max-width:400px">
            <input type="text" name="search" class="form-control" placeholder="Cari judul/penerbit/pengarang/tahun/kategori/sub" value="{{ request('search') }}">
            <button class="btn btn-dark">Cari</button>
            @if(request('search'))
                <a href="{{route('books.index')}}" class="btn btn-dark">Reset</a>
            @endif
        </div>
    </form>
    <!-- end search-->

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{session('success')}}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{session('error')}}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ✅ ALERT UNTUK KONSUMEN BELUM VERIFIED --}}
    @php
        $member = \App\Models\Members::where('id_member', Auth::id())->first();
        $isVerified = $member && $member->status === 'verified';
        $isKonsumen = Auth::user()->role === 'konsumen';
    @endphp

    @if($isKonsumen && !$isVerified)
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Perhatian!</strong> Anda belum bisa meminjam buku karena status member Anda belum diverifikasi.
            Silakan tunggu verifikasi dari admin atau
            <a href="{{ route('members.index') }}" class="alert-link">cek status member Anda</a>.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
            <tr class="text-center">
                <th width="5%">ID</th>
                <th width="50%">Judul</th>
                <th width="10%">Jumlah</th>
                <th width="35%">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($book as $b)
                <tr>
                    <td class="text-center">{{ ($book->currentPage() - 1) * $book->perPage() + $loop->iteration }}</td>
                    <td>
                        <strong>{{ $b->judul }}</strong>
                        <br>
                        <small class="text-muted">
                            {{ $b->pengarang }} • {{ $b->publisher->nama_penerbit }} • {{ $b->tahun_terbit }}
                        </small>
                    </td>
                    <td class="text-center">
                        <span class="badge
                            @if($b->jumlah_tata == 0)
                                bg-danger
                            @elseif($b->jumlah_tata < $b->jumlah)
                                bg-warning
                            @else
                                bg-success
                            @endif">
                            {{ $b->jumlah_tata ?? 0 }}
                        </span>
                        <span class="text-muted">/ {{ $b->jumlah }}</span>
                    </td>
                    <td class="text-center">
                        <!-- Tombol Show/Detail -->
                        <a href="{{ route('books.show', $b->id_buku) }}" class="btn btn-sm btn-info" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>

                        {{-- ✅ TOMBOL PINJAM UNTUK KONSUMEN VERIFIED --}}
                        @if($isKonsumen && $isVerified)
                            @if($b->jumlah_tata > 0)
                                <button class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalPinjam{{ $b->id_buku }}"
                                        title="Pinjam Buku">
                                    <i class="fas fa-book-reader"></i> Pinjam
                                </button>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled title="Stok Kosong">
                                    <i class="fas fa-ban"></i> Kosong
                                </button>
                            @endif
                        @endif

                        <!-- tombol edit (Admin/Petugas Only) -->
                        @unless(Auth::user()->role === 'konsumen')
                            <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal" data-bs-target="#modalEditBuku{{ $b->id_buku }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endunless

                        <!-- Tombol Hapus (Admin/Petugas Only) -->
                        @unless(Auth::user()->role === 'konsumen')
                            <button type="button" class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal" data-bs-target="#modalHapus{{ $b->id_buku }}" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-4">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data buku</p>
                    </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $book->links('pagination::bootstrap-5') }}
    </div>

    {{-- ✅ MODAL PINJAM BUKU (UNTUK KONSUMEN) --}}
    @if($isKonsumen && $isVerified)
        @foreach($book as $b)
            <div class="modal fade" id="modalPinjam{{ $b->id_buku }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-book-reader me-2"></i> Pinjam Buku
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('borrowing.borrow') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_buku" value="{{ $b->id_buku }}">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <strong>Judul Buku:</strong>
                                    <p class="mb-1">{{ $b->judul }}</p>
                                    <small class="text-muted">{{ $b->pengarang }} • {{ $b->publisher->nama_penerbit }}</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tanggal Pengembalian <span class="text-danger">*</span></label>
                                    <input type="date"
                                           name="pengembalian"
                                           class="form-control"
                                           min="{{ date('Y-m-d', strtotime('+7 day')) }}"
                                           max="{{ date('Y-m-d', strtotime('+7 days')) }}"
                                           value="{{ date('Y-m-d', strtotime('+7 days')) }}"
                                           required>
                                    <small class="text-muted">Maksimal 14 hari dari hari ini</small>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>
                                        <strong>Info:</strong> Buku yang dipinjam akan otomatis dipilihkan eksemplar yang tersedia.
                                    </small>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i> Pinjam Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- hapus buku -->
    @foreach($book as $b)
        <div class="modal fade" id="modalHapus{{ $b->id_buku }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Yakin ingin menghapus buku <b>{{ $b->judul }}</b>?
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('books.destroy', $b->id_buku) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!-- end hapus buku -->

    <!-- Modal Tambah Buku -->
    <div class="modal fade" id="modalTambahBuku" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('books.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Judul</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Penerbit</label>
                            <select name="id_penerbit" class="form-control">
                                <option value="">-- Pilih Penerbit --</option>
                                @foreach($publisher as $p)
                                    <option value="{{ $p->id_penerbit }}">{{ $p->nama_penerbit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Pengarang</label>
                            <input type="text" name="pengarang" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Tahun Terbit</label>
                            <input type="text" name="tahun_terbit" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Kategori</label>
                            <select name="id_kategori" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $k)
                                    <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Sub Kategori</label>
                            <select name="id_subkategori" class="form-control">
                                <option value="">-- Pilih Sub Kategori --</option>
                                @foreach($subcategories as $s)
                                    <option value="{{ $s->id_subkategori }}">{{ $s->nama_subkategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>ISBN</label>
                            <input type="text" name="isbn" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Jumlah</label>
                            <input type="number" name="jumlah" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Tambah -->


    <!-- modal edit buku -->
    @foreach($book as $book)
        <!-- Modal Edit Buku -->
        <div class="modal fade" id="modalEditBuku{{ $book->id_buku }}" tabindex="-1" aria-labelledby="modalEditBukuLabel{{ $book->id_buku }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="modalEditBukuLabel{{ $book->id_buku }}">Edit Buku</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <form action="{{ route('books.update', $book->id_buku) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">

                            <div class="mb-2">
                                <label class="form-label">Judul Buku</label>
                                <input type="text" name="judul" class="form-control" value="{{ $book->judul }}" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Pengarang</label>
                                <input type="text" name="pengarang" class="form-control" value="{{ $book->pengarang }}" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Penerbit</label>
                                <select name="id_penerbit" id="id_penerbit_{{ $book->id_penerbit }}" class="form-control" required>
                                    <option value="">-- Pilih penerbit --</option>
                                    @foreach($publisher as $p)
                                        <option value="{{ $p->id_penerbit }}"
                                            {{ old('id_penerbit', $book->id_penerbit) == $book->id_penerbit ? 'selected' : '' }}>
                                            {{ $p->nama_penerbit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Kategori</label>
                                <select name="id_kategori" id="id_kategori_{{ $book->id_kategori }}" class="form-control" required>
                                    <option value="">-- Pilih kategori --</option>
                                    @foreach($categories as $k)
                                        <option value="{{ $k->id_kategori }}"
                                            {{ old('id_kategori', $book->id_kategori) == $book->id_kategori ? 'selected' : '' }}>
                                            {{ $k->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Sub Kategori</label>
                                <select name="id_subkategori" id="id_subkategori_{{ $book->id_subkategori }}"class="form-control" required>
                                    <option value="">-- Pilih Sub Kategori --</option>
                                    @foreach($subcategories as $sk)
                                        <option value="{{ $sk->id_subkategori }}"
                                            {{ old('id_subkategori',$book->id_subkategori )== $book->id_subkategori ? 'selected' : '' }}>
                                            {{ $sk->nama_subkategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Tahun Terbit</label>
                                <input type="text" name="tahun_terbit" class="form-control" value="{{ $book->tahun_terbit }}" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">ISBN</label>
                                <input type="text" name="isbn" class="form-control" value="{{ $book->isbn }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Barcode</label>
                                <input type="text" name="barcode" class="form-control" value="{{ $book->barcode }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="jumlah" class="form-control" value="{{ $book->jumlah }}">
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
    <!-- end edit buku-->

@endsection
