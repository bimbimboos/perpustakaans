<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && in_array(strtolower(auth()->user()->role), ['admin', 'petugas']);
    }

    public function rules()
    {
        $memberId = $this->route('id_member');

        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'email:rfc,dns', Rule::unique('members', 'email')->ignore($memberId, 'id_member'), 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'no_telp' => ['required', 'string', 'regex:/^(08|62)\d{8,13}$/', Rule::unique('members', 'no_telp')->ignore($memberId, 'id_member')],
            'alamat' => ['required', 'string', 'max:500'],
            'ktp_number' => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'ktp_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'status' => ['nullable', Rule::in(['pending', 'verified', 'rejected'])],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi',
            'name.regex' => 'Nama hanya boleh mengandung huruf dan spasi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.regex' => 'Password harus mengandung huruf besar, kecil, dan angka',
            'no_telp.required' => 'Nomor telepon wajib diisi',
            'no_telp.regex' => 'Format nomor telepon tidak valid',
            'no_telp.unique' => 'Nomor telepon sudah terdaftar',
            'alamat.required' => 'Alamat wajib diisi',
            'ktp_number.size' => 'Nomor KTP harus 16 digit',
            'ktp_number.regex' => 'Nomor KTP hanya boleh berisi angka',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('no_telp')) {
            $this->merge([
                'no_telp' => preg_replace('/[^0-9]/', '', $this->no_telp)
            ]);
        }
    }
}
