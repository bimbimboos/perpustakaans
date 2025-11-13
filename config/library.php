<?php

return [
    // Apakah user publik (belum login) bisa registrasi member?
    'allow_public_registration' => env('ALLOW_PUBLIC_REGISTRATION', false),

    // Atau hanya user yang sudah login dengan role konsumen?
    'require_auth_for_registration' => env('REQUIRE_AUTH_FOR_REGISTRATION', true),

    // Max file size untuk KTP (dalam KB)
    'max_ktp_file_size' => env('MAX_KTP_FILE_SIZE', 5120), // 5MB

    // Max file size untuk photo (dalam KB)
    'max_photo_file_size' => env('MAX_PHOTO_FILE_SIZE', 2048), // 2MB
];
