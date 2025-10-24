@extends('layouts.app')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detail Penataan Buku</h2>
        <a href="{{ route('sortbooks.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <!-- Detail penataan -->
    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>ID Penataan:</strong> {{ $sort->id_penataan }}</p>
            <p><strong>Nama Buku:</strong> {{ $sort->books->nama_buku ?? 'Buku tidak ditemukan' }}</p>
            <p><strong>Nama Rak:</strong> {{ $sort->racks->nama_rak ?? 'Rak tidak ditemukan' }}</p>
            <p><strong>Kolom:</strong> {{ $sort->kolom }}</p>
            <p><strong>Baris:</strong> {{ $sort->baris }}</p>
            <p><strong>Jumlah:</strong> {{ $sort->jumlah }}</p>
            <p><strong>Petugas:</strong> {{ $sort->user->name ?? '-' }}</p>
            <p><strong>Tanggal Dibuat:</strong> {{ $sort->insert_date->format('d-m-Y H:i') }}</p>
            <p><strong>Tanggal Diperbarui:</strong> {{ $sort->modified_date->format('d-m-Y H:i') }}</p>
        </div>
    </div>
@endsection
