@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-3">Detail Lokasi Rak</h2>

        <div class="mb-3">
            <a href="{{ route('rackslocation.index') }}" class="btn btn-secondary">← Kembali ke Daftar</a>
        </div>

        <div class="card p-3">
            <p><strong>ID Lokasi:</strong> {{ $rackslocation->id_lokasi }}</p>
            <p><strong>Lantai:</strong> {{ $rackslocation->lantai }}</p>
            <p><strong>Ruang:</strong> {{ $rackslocation->ruang }}</p>
            <p><strong>Sisi:</strong> {{ $rackslocation->sisi ?? '-' }}</p>
        </div>
    </div>
@endsection
