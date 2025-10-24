<?php
// app/Http/Requests/StoreMemberRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang bisa create member (atau public registration tergantung logic)
        return $this->user()?->role === 'admin' || config('library.allow_public_registration');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:members,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'alamat' => ['required', 'string', 'max:500'],
            'no_telp' => ['required', 'string', 'regex:/^[0-9+\-\s()]{8,20}$/'],

            // KTP number - 16 digit untuk Indonesia
            'ktp_number' => [
                'required',
                'string',
                'regex:/^[0-9]{16}$/',
                'unique:members,ktp_hash', // Check hash untuk prevent duplicate
            ],

            // KTP Photo - max 5MB
            'ktp_photo' => [
                'required',
                'file',
                'mimes:jpeg,png,pdf',
                'max:5120', // 5MB
            ],

            // Profile Photo - max 2MB
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048', // 2MB
            ],

            'role' => ['sometimes', 'in:member,admin'],
            'status' => ['sometimes', 'in:active,inactive,suspended'],
        ];
    }

    public function messages(): array
    {
        return [
            'ktp_number.regex' => 'Nomor KTP harus 16 digit angka.',
            'ktp_number.unique' => 'Nomor KTP sudah terdaftar dalam sistem.',
            'ktp_photo.mimes' => 'File KTP harus berformat JPEG, PNG, atau PDF.',
            'ktp_photo.max' => 'Ukuran file KTP maksimal 5MB.',
        ];
    }

    /**
     * Custom validation untuk check KTP hash uniqueness
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('ktp_number')) {
            // Tambahkan hash untuk validation
            $this->merge([
                'ktp_hash_check' => hash('sha256', $this->ktp_number)
            ]);
        }
    }

    /**
     * Tambahan: validate KTP hash sebelum insert
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('ktp_number')) {
                $hash = hash('sha256', $this->ktp_number);
                $exists = \App\Models\Members::where('ktp_hash', $hash)->exists();

                if ($exists) {
                    $validator->errors()->add('ktp_number', 'Nomor KTP sudah terdaftar dalam sistem.');
                }
            }
        });
    }
}
