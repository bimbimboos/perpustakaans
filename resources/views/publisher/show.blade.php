@extends('layouts.app')
@section('content')
    <div class="container">
        <h2 class="mb-3">Detail Penerbit</h2>
        <div class="mb-3">
            <a href="{{route('publisher.index')}}" class="btn btn-secondary">← Kembali ke Daftar</a>
        </div>
        <div class="card p-3">
            <p><strong>ID:</strong>{{$publisher->id_penerbit}}</p>
            <p><strong>Alamat:</strong>{{$publisher->alamat}}</p>
            <p><strong>Telepon:</strong>{{$publisher->no_telepon}}</p>
            <p><strong>Email:</strong>{{$publisher->email}}</p>
        </div>
    </div>
@endsection
