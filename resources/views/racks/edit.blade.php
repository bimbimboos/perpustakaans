@extends('layouts.app')

@section('content')
    <h2>Edit Rak</h2>

    <form action="{{ route('racks.update', $rack->id_rak) }}" method="POST">
        @csrf
        @method('PATCH') {{-- metode untuk update --}}

        <div class="mb-3">
            <label>Barcode</label>
            <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $rack->barcode) }}" required>
        </div>
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama', $rack->nama) }}">
        </div>
        <div class="mb-3">
            <label>Kolom</label>
            <input type="text" name="kolom" class="form-control" value="{{ old('kolom', $rack->kolom) }}">
        </div>
        <div class="mb-3">
            <label>Baris</label>
            <input type="text" name="baris" class="form-control" value="{{ old('baris', $rack->baris) }}">
        </div>
        <div class="mb-3">
            <label>Kapasitas</label>
            <input type="text" name="kapasitas" class="form-control" value="{{ old('kapasitas', $rack->kapasitas) }}">
        </div>
        <div class="mb-3">
            <label>Lokasi</label>
            <input type="text" name="id_lokasi" class="form-control" value="{{ old('id_lokasi', $rack->id_lokasi) }}">
        </div>
        <div class="mb-3">
            <label>Kategori</label>
            <input type="text" name="id_kategori" class="form-control" value="{{ old('id_kategori', $rack->id_kategori) }}">
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('racks.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection
