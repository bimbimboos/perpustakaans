{{-- resources/views/members/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header with Actions --}}
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('members.index') }}" class="text-gray-600 hover:text-gray-900 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Detail Member</h1>
                            <p class="text-gray-600 mt-1">Informasi lengkap member perpustakaan</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        @can('update', $member)
                            <a href="{{ route('members.edit', $member) }}"
                               class="inline-flex items-center px-5 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg shadow transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        @endcan

                        @can('delete', $member)
                            <button onclick="confirmDelete()"
                                    class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column - Profile Card --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                        {{-- Profile Photo --}}
                        <div class="flex flex-col items-center">
                            <div class="h-32 w-32 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-4xl shadow-lg mb-4">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 text-center">{{ $member->name }}</h2>
                            <p class="text-gray-500 text-sm mt-1">ID: #{{ $member->id_user }}</p>

                            {{-- Status Badge --}}
                            <div class="mt-4 flex gap-2">
                                @if($member->status === 'active')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 mr-1.5 bg-green-500 rounded-full"></span>
                                Active
                            </span>
                                @elseif($member->status === 'inactive')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <span class="w-2 h-2 mr-1.5 bg-gray-500 rounded-full"></span>
                                Inactive
                            </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <span class="w-2 h-2 mr-1.5 bg-red-500 rounded-full"></span>
                                Suspended
                            </span>
                                @endif

                                @if($member->role === 'admin')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Admin
                            </span>
                                @endif
                            </div>
                        </div>

                        {{-- Quick Info --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 space-y-4">
                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-gray-700 truncate">{{ $member->email }}</span>
                            </div>

                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="text-gray-700">{{ $member->no_telp }}</span>
                            </div>

                            <div class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 11c0-3.314-2.239-6-5-6S2 7.686 2 11s2.239 6 5 6 5-2.686 5-6zM12 11h10"/>
                                </svg>
                                <span class="text-gray-700">{{ $member->alamat ?? 'Alamat belum diisi' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Member Details --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Data Pribadi</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                <dd class="text-gray-900">{{ $member->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-gray-900">{{ $member->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nomor Telepon</dt>
                                <dd class="text-gray-900">{{ $member->no_telp ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                <dd class="text-gray-900">{{ $member->alamat ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Daftar</dt>
                                <dd class="text-gray-900">{{ $member->created_at->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Akun</dt>
                                <dd>
                                    @if($member->status === 'active')
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Aktif</span>
                                    @elseif($member->status === 'inactive')
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Nonaktif</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Suspended</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Dokumen KTP dan Foto --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Dokumen Keanggotaan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-700 mb-2">Foto Profil</h4>
                                @if($member->foto)
                                    <img src="{{ asset('storage/' . $member->foto) }}" alt="Foto Member"
                                         class="w-48 h-48 object-cover rounded-lg shadow">
                                @else
                                    <div class="w-48 h-48 flex items-center justify-center bg-gray-100 text-gray-400 rounded-lg">
                                        Tidak ada foto
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h4 class="font-medium text-gray-700 mb-2">KTP / Identitas</h4>
                                @if($member->ktp)
                                    <a href="{{ route('members.downloadKtp', $member->id_user) }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Unduh KTP
                                    </a>
                                @else
                                    <p class="text-gray-500 text-sm">Belum ada KTP yang diunggah.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Riwayat Peminjaman --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Riwayat Peminjaman</h3>
                        @if($member->borrowings && $member->borrowings->count() > 0)
                            <table class="w-full text-left text-sm">
                                <thead>
                                <tr class="text-gray-600 border-b">
                                    <th class="py-2">Judul Buku</th>
                                    <th class="py-2">Tanggal Pinjam</th>
                                    <th class="py-2">Batas Kembali</th>
                                    <th class="py-2">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($member->borrowings as $borrow)
                                    <tr class="border-b hover:bg-gray-50 transition">
                                        <td class="py-2">{{ $borrow->book->judul ?? '-' }}</td>
                                        <td class="py-2">{{ $borrow->tgl_pinjam->format('d M Y') }}</td>
                                        <td class="py-2">{{ $borrow->batas_kembali->format('d M Y') }}</td>
                                        <td class="py-2">
                                            @if($borrow->status == 'dipinjam')
                                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Dipinjam</span>
                                            @elseif($borrow->status == 'dikembalikan')
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Dikembalikan</span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Terlambat</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-500">Belum ada riwayat peminjaman.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation --}}
    <script>
        function confirmDelete() {
            if (confirm("Apakah Anda yakin ingin menghapus member ini?")) {
                fetch("{{ route('members.destroy', $member) }}", {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                }).then(() => {
                    window.location.href = "{{ route('members.index') }}";
                });
            }
        }
    </script>
@endsection
