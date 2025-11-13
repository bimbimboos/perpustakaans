@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Detail Buku</h1>
            <div>
                <a href="{{ route('books.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                @unless(Auth::user()->role === 'konsumen')
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditBuku{{ $book->id_buku }}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endunless
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Info Buku -->
            <div class="col-md-8">
                <div class="card mb-5">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informasi Buku</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Judul</strong></div>
                            <div class="col-md-9">{{ $book->judul }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Penerbit</strong></div>
                            <div class="col-md-9">{{ $book->publisher->nama_penerbit }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Pengarang</strong></div>
                            <div class="col-md-9">{{ $book->pengarang }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Tahun Terbit</strong></div>
                            <div class="col-md-9">{{ $book->tahun_terbit }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Kategori</strong></div>
                            <div class="col-md-9">
                                <span class="badge bg-info">{{ $book->categories->nama_kategori }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Sub Kategori</strong></div>
                            <div class="col-md-9">{{ $book->subcategories ? $book->subcategories->nama_subkategori : '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>ISBN</strong></div>
                            <div class="col-md-9">{{ $book->isbn }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Barcode</strong></div>
                            <div class="col-md-9">{{ $book->barcode }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Statistik Buku</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <h6 class="text-muted">Jumlah Total</h6>
                            <h2 class="text-primary">{{ $book->jumlah }}</h2>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <h6 class="text-muted">Tersedia</h6>
                            <h2 class="text-success">{{ $book->jumlah_tata ?? 0 }}</h2>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <h6 class="text-muted">Dipinjam</h6>
                            <h2 class="text-danger">{{ $book->jumlah - ($book->jumlah_tata ?? 0) }}</h2>
                        </div>
                        <hr>

                        <!-- ✅ TOMBOL LIHAT EKSEMPLAR (UNTUK SEMUA ROLE) -->
                        <a href="{{ route('books.items.index', $book->id_buku) }}" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="fas fa-eye"></i> Lihat Eksemplar
                        </a>

                        @unless(Auth::user()->role === 'konsumen')
                            <a href="{{ route('books.items.index', $book->id_buku) }}" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-cog"></i> Kelola Eksemplar
                            </a>
                        @endunless
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Buku (sama seperti sebelumnya) -->
        <div class="modal fade" id="modalEditBuku{{ $book->id_buku }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title">Edit Buku</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                <select name="id_penerbit" class="form-control" required>
                                    <option value="">-- Pilih penerbit --</option>
                                    @foreach($publisher as $p)
                                        <option value="{{ $p->id_penerbit }}"
                                            {{ $book->id_penerbit == $p->id_penerbit ? 'selected' : '' }}>
                                            {{ $p->nama_penerbit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Kategori</label>
                                <select name="id_kategori" class="form-control" required>
                                    <option value="">-- Pilih kategori --</option>
                                    @foreach($categories as $k)
                                        <option value="{{ $k->id_kategori }}"
                                            {{ $book->id_kategori == $k->id_kategori ? 'selected' : '' }}>
                                            {{ $k->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Sub Kategori</label>
                                <select name="id_subkategori" class="form-control" required>
                                    <option value="">-- Pilih Sub Kategori --</option>
                                    @foreach($subcategories as $sk)
                                        <option value="{{ $sk->id_subkategori }}"
                                            {{ $book->id_subkategori == $sk->id_subkategori ? 'selected' : '' }}>
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

@endsection
