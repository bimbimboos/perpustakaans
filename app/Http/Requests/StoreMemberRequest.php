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
            'name' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'email' => ['nullable', 'email:rfc', 'max:255', 'unique:members,email'],
            'agama' => ['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu'],
            'alamat' => ['required', 'string', 'max:500'],
            'institusi' => ['nullable', 'string', 'max:255'],
            'alamat_institusi' => ['nullable', 'string', 'max:500'],
            'jenjang_pendidikan' => ['required', 'in:SD,SMP,SMA/SMK,D3,S1,S2,S3,Umum'],
            'no_telp' => ['required', 'string', 'regex:/^(08|62)\d{8,13}$/'],
            'no_hp_ortu' => ['nullable', 'string', 'regex:/^(08|62)\d{8,13}$/'],
            'ktp_number' => ['required', 'string', 'min:10', 'max:20'], // Bisa KTP atau Kartu Pelajar
            'ktp_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'agama.required' => 'Agama wajib dipilih',
            'alamat.required' => 'Alamat wajib diisi',
            'jenjang_pendidikan.required' => 'Jenjang pendidikan wajib dipilih',
            'no_telp.required' => 'Nomor HP wajib diisi',
            'no_telp.regex' => 'Format nomor HP tidak valid',
            'ktp_number.required' => 'Nomor identitas wajib diisi',
            'ktp_photo.required' => 'Foto KTP/Kartu Pelajar wajib diupload',
        ];
    }
}
