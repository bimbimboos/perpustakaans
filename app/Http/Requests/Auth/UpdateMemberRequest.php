<?php
// app/Http/Requests/UpdateMemberRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        $member = $this->route('member');
        return $this->user()->can('update', $member);
    }

    public function rules(): array
    {
        $memberId = $this->route('member')->id_user;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('members', 'email')->ignore($memberId, 'id_user')
            ],
            'password' => ['sometimes', 'nullable', 'confirmed', Password::defaults()],
            'alamat' => ['sometimes', 'required', 'string', 'max:500'],
            'no_telp' => ['sometimes', 'required', 'string', 'regex:/^[0-9+\-\s()]{8,20}$/'],

            // KTP update - optional
            'ktp_number' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^[0-9]{16}$/',
            ],

            'ktp_photo' => [
                'sometimes',
                'nullable',
                'file',
                'mimes:jpeg,png,pdf',
                'max:5120',
            ],

            'photo' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048',
            ],

            'role' => ['sometimes', 'in:member,admin'],
            'status' => ['sometimes', 'in:active,inactive,suspended'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Jika update KTP, check uniqueness
            if ($this->has('ktp_number') && $this->ktp_number) {
                $hash = hash('sha256', $this->ktp_number);
                $memberId = $this->route('member')->id_user;

                $exists = \App\Models\Member::where('ktp_hash', $hash)
                    ->where('id_user', '!=', $memberId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('ktp_number', 'Nomor KTP sudah terdaftar dalam sistem.');
                }
            }
        });
    }
}
