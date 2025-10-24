@extends('layouts.app')
@section('content')
    <h2 class="mb-3">Tambah Item untuk Buku: {{ $book->judul }}</h2>

    <form action="{{ route('books.items.store', $book->id_buku) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="kondisi" class="form-label">Kondisi</label>
            <select name="kondisi" id="kondisi" class="form-control">
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
                <option value="hilang">Hilang</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="tersedia">Tersedia</option>
                <option value="dipinjam">Dipinjam</option>
                <option value="hilang">Hilang</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="sumber" class="form-label">Sumber</label>
            <input type="text" name="sumber" id="sumber" value="{{ old('sumber') }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="id_rak" class="form-label">Pilih Rak</label>
            <select name="id_rak" id="id_rak" class="form-control" required>
                @foreach($racks as $rack)
                    <option value="{{ $rack->id_rak }}">
                        {{ $rack->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Tambah Item</button>
        <a href="{{ route('books.items.index', $book->id_buku) }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection
