<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'password' => 'required|string|min:8|confirmed',
            'no_telp' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'ktp_number' => 'required|string|size:16|unique:members,ktp_hash,' . hash('sha256', request('ktp_number')),
            'ktp_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'no_telp.required' => 'Nomor telepon wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'ktp_number.required' => 'Nomor KTP wajib diisi',
            'ktp_number.size' => 'Nomor KTP harus 16 digit',
            'ktp_number.unique' => 'Nomor KTP sudah terdaftar',
            'ktp_photo.required' => 'Foto KTP wajib diupload',
            'ktp_photo.image' => 'File harus berupa gambar',
            'ktp_photo.max' => 'Ukuran foto KTP maksimal 2MB',
        ];
    }
}
