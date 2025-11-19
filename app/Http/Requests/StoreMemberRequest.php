<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && in_array(strtolower(auth()->user()->role), ['admin', 'petugas']);
    }

    protected function prepareForValidation()
    {
        // 1. BERSIHKAN NOMOR TELEPON (Hapus spasi, strip, dll)
        if ($this->has('no_telp')) {
            $this->merge([
                'no_telp' => preg_replace('/[^0-9]/', '', $this->no_telp)
            ]);
        }

        // 2. BERSIHKAN NOMOR KTP (Hapus spasi jika user copy-paste)
        if ($this->has('ktp_number')) {
            $this->merge([
                'ktp_number' => preg_replace('/[^0-9]/', '', $this->ktp_number)
            ]);
        }
    }

    public function rules()
    {
        return [
            // Update: Boleh ada titik (.) dan petik (') untuk nama gelar atau marga
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],

            'email' => ['nullable', 'email:rfc,dns', 'max:255', 'unique:members,email'],

            // Update: Disesuaikan dengan UI (Min 8, harus ada angka dan huruf)
            'password' => [
                'required',
                'string',
                'min:8',             // Minimal 8 karakter (sebelumnya 6)
                'regex:/[0-9]/',     // Harus ada angka
                'regex:/[a-zA-Z]/',  // Harus ada huruf
            ],

            'no_telp' => ['required', 'string', 'regex:/^(08|62)\d{8,13}$/', 'unique:members,no_telp'],

            'alamat' => ['required', 'string', 'max:500'],

            // Update: Pakai 'digits:16' lebih rapi daripada 'size' + 'regex' manual
            'ktp_number' => ['required', 'string', 'digits:16'],

            'ktp_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],

            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi',
            'name.regex' => 'Nama mengandung karakter yang tidak diizinkan',

            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar, gunakan email lain',

            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.regex' => 'Password harus mengandung kombinasi huruf dan angka',

            'no_telp.required' => 'Nomor telepon wajib diisi',
            'no_telp.regex' => 'Nomor telepon harus diawali 08 atau 62',
            'no_telp.unique' => 'Nomor telepon sudah terdaftar',

            'alamat.required' => 'Alamat wajib diisi',

            'ktp_number.required' => 'Nomor KTP wajib diisi',
            'ktp_number.digits' => 'Nomor KTP harus tepat 16 digit angka',

            'ktp_photo.required' => 'Foto KTP wajib diupload',
            'ktp_photo.image' => 'File KTP harus berupa gambar',
            'ktp_photo.mimes' => 'Format foto KTP harus jpeg, jpg, atau png',
            'ktp_photo.max' => 'Ukuran foto KTP maksimal 4MB',

            'photo.image' => 'Foto profil harus berupa gambar',
            'photo.mimes' => 'Format foto harus jpeg, jpg, atau png',
            'photo.max' => 'Ukuran foto maksimal 4MB',
        ];
    }
}
