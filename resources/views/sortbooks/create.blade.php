@extends('layouts.app')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Tambah Penataan Buku</h2>
        <a href="{{ route('sortbooks.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <!-- Pesan error validasi -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form tambah penataan -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('sortbooks.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="id_buku">Buku</label>
                    <select id="id_buku" name="id_buku" class="form-control" required>
                        <option value="">-- Pilih Buku --</option>
                        @foreach ($books as $b)
                            <option value="{{ $b->id_buku }}">{{ $b->judul }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_rak">Rak</label>
                    <select id="id_rak" name="id_rak" class="form-control" required>
                        <option value="">-- Pilih Rak --</option>
                    </select>
                </div>


                <!-- Petugas: Auto tampil nama login (readonly), nggak ada input -->
                <div class="mb-3">
                    <label>Petugas</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                    <input type="hidden" name="id_user" value="{{ Auth::id() }}">  <!-- Kirim ID auto -->
                </div>
                <div class="mb-3">
                    <label for="kolom">Kolom</label>
                    <input type="number" name="kolom" id="kolom" class="form-control"
                           value="{{ old('kolom') }}" min="1" required>
                    @error('kolom')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="baris">Baris</label>
                    <input type="number" name="baris" id="baris" class="form-control"
                           value="{{ old('baris') }}" min="1" required>
                    @error('baris')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" name="jumlah" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                           value="{{ old('jumlah', $sort->jumlah ?? '') }}" min="1" required>
                    @error('jumlah')
                    <div class="invalid-feedback">{{ $message }}</div>  <!-- Tampil pesan over-eksemplar -->
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="sumber">Sumber</label>
                    <input type="text" name="sumber" id="sumber" class="form-control @error('sumber') is-invalid @enderror"
                           value="{{ old('sumber', $sort->sumber ?? '') }}" min="1" required>
                    @error('sumber')
                    <div class="invalid-feedback">{{ $message }}</div>  <!-- Tampil pesan over-eksemplar -->
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookSelect = document.getElementById('id_buku');
            const rackSelect = document.getElementById('id_rak');

            bookSelect.addEventListener('change', function() {
                const idBuku = this.value;
                rackSelect.innerHTML = '<option value="">-- Pilih Rak --</option>';

                if (idBuku) {
                    fetch(`/get-rak-by-buku/${idBuku}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.length === 0) {
                                rackSelect.innerHTML = '<option value="">Tidak ada rak tersedia</option>';
                            } else {
                                data.forEach(rak => {
                                    rackSelect.innerHTML += `<option value="${rak.id_rak}">${rak.nama}</option>`;
                                });
                            }
                        });
                }
            });
        });
    </script>


@endsection
