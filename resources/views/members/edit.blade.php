{{-- resources/views/members/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <a href="{{ route('members.show', $member) }}" class="text-gray-600 hover:text-gray-900 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Member</h1>
                        <p class="text-gray-600 mt-1">{{ $member->name }}</p>
                    </div>
                </div>
            </div>

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-red-800">Terdapat kesalahan:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form Card --}}
            <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Personal Info Section --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Informasi Pribadi</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Lengkap --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $member->name) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('name') border-red-500 @enderror">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $member->email) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('email') border-red-500 @enderror">
                            @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No Telepon --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                No. Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="no_telp"
                                   value="{{ old('no_telp', $member->no_telp) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('no_telp') border-red-500 @enderror">
                            @error('no_telp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Password Baru
                            </label>
                            <input type="password"
                                   name="password"
                                   placeholder="Kosongkan jika tidak diubah"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('password') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password</p>
                            @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Konfirmasi Password Baru
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   placeholder="Ulangi password baru"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        {{-- Alamat --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea name="alamat"
                                      rows="3"
                                      required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('alamat') border-red-500 @enderror">{{ old('alamat', $member->alamat) }}</textarea>
                            @error('alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- KTP Section --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-yellow-100 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Informasi KTP</h2>
                                <p class="text-sm text-gray-500">Update data KTP jika diperlukan</p>
                            </div>
                        </div>
                        @if($member->isKtpVerified())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Terverifikasi
                    </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Belum Verifikasi
                    </span>
                        @endif
                    </div>

                    <div class="space-y-6">
                        @if(auth()->user()->role === 'admin')
                            {{-- Show Masked KTP untuk Admin --}}
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-blue-800">
                                            Nomor KTP saat ini: <span class="font-mono">{{ $member->getMaskedKtp() }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Nomor KTP Baru (Optional) --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nomor KTP Baru (16 digit)
                            </label>
                            <input type="text"
                                   name="ktp_number"
                                   id="ktp_number"
                                   value="{{ old('ktp_number') }}"
                                   maxlength="16"
                                   placeholder="Kosongkan jika tidak diubah"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono text-lg tracking-wider focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('ktp_number') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah nomor KTP</p>
                            @error('ktp_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Upload KTP Photo Baru --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Foto/Scan KTP Baru
                            </label>

                            @if($member->ktp_photo_path)
                                <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">File KTP sudah ada</span>
                                        </div>
                                        @can('viewKtp', $member)
                                            <a href="{{ $member->getKtpPhotoUrl() }}"
                                               target="_blank"
                                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                Lihat File
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                    <div id="ktpUploadPlaceholder" class="flex flex-col items-center justify-center py-4">
                                        <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="text-xs text-gray-500">Upload file baru (JPEG, PNG, PDF - Max 5MB)</p>
                                    </div>
                                    <div id="ktpPreview" class="hidden w-full h-full p-2">
                                        <img src="" alt="Preview KTP" class="w-full h-full object-contain rounded-lg">
                                    </div>
                                    <input type="file"
                                           name="ktp_photo"
                                           id="ktp_photo"
                                           accept="image/jpeg,image/png,application/pdf"
                                           onchange="previewFile('ktp_photo', 'ktpPreview', 'ktpUploadPlaceholder')"
                                           class="hidden">
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Upload file baru akan mengganti file lama</p>
                            @error('ktp_photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Photo Profile Section --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-purple-100 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Foto Profil</h2>
                            <p class="text-sm text-gray-500">Update foto profil member</p>
                        </div>
                    </div>

                    <div>
                        @if($member->photo_path)
                            <div class="mb-3 flex items-center gap-3">
                                <div class="h-16 w-16 rounded-full overflow-hidden bg-gradient-to-br from-blue-500 to-purple-600">
                                    <div class="w-full h-full flex items-center justify-center text-white font-bold text-2xl">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Foto profil saat ini</p>
                                    <p class="text-xs text-gray-500">Upload baru untuk mengganti</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                <div id="photoUploadPlaceholder" class="flex flex-col items-center justify-center py-4">
                                    <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-xs text-gray-500">Upload foto baru (JPEG, PNG - Max 2MB)</p>
                                </div>
                                <div id="photoPreview" class="hidden w-full h-full p-2">
                                    <img src="" alt="Preview Photo" class="w-24 h-24 object-cover rounded-full mx-auto">
                                </div>
                                <input type="file"
                                       name="photo"
                                       id="photo"
                                       accept="image/jpeg,image/png,image/jpg"
                                       onchange="previewFile('photo', 'photoPreview', 'photoUploadPlaceholder')"
                                       class="hidden">
                            </label>
                        </div>
                        @error('photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Admin Options --}}
                @if(auth()->user()->role === 'admin')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-indigo-100 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Pengaturan Admin</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                                <select name="role" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                    <option value="member" {{ $member->role === 'member' ? 'selected' : '' }}>Member</option>
                                    <option value="admin" {{ $member->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                    <option value="active" {{ $member->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $member->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ $member->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('members.show', $member) }}"
                       class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Member
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Preview file upload
            function previewFile(inputId, previewId, placeholderId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);
                const file = input.files[0];

                if (file) {
                    if (file.type === 'application/pdf') {
                        placeholder.innerHTML = `
                <div class="text-center py-2">
                    <svg class="w-12 h-12 mx-auto mb-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs font-semibold text-gray-700">${file.name}</p>
                    <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                </div>
            `;
                    } else {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.querySelector('img').src = e.target.result;
                            preview.classList.remove('hidden');
                            placeholder.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }

            // Validate KTP number (hanya angka)
            const ktpInput = document.getElementById('ktp_number');
            if (ktpInput) {
                ktpInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/\D/g, '').substring(0, 16);
                });
            }
        </script>
    @endpush

@endsection
