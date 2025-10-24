<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        // DISK LOCAL - untuk file umum/temporary
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),  // ⬅️ SAYA KEMBALIKAN KE DEFAULT
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        // DISK PUBLIC - untuk file yang boleh diakses publik (gambar artikel, dll)
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        // ========================================
        // ⬇️ TAMBAHAN BARU: DISK PRIVATE
        // ========================================
        // Untuk file SENSITIF seperti KTP, dokumen pribadi
        // File di sini TIDAK BISA diakses langsung via URL
        // Hanya bisa diakses lewat Controller dengan authorization
        'private' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),  // ⬅️ Folder khusus untuk file sensitif
            'visibility' => 'private',              // ⬅️ PENTING: set private
            'throw' => 'private',
            'report' => false,
        ],

        // S3 - jika nanti mau pakai cloud storage (optional)
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // ========================================
        // ⬇️ TAMBAHAN: S3 PRIVATE (jika pakai AWS)
        // ========================================
        // Uncomment jika sudah siap pakai AWS S3 untuk production
        /*
        's3_private' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'ap-southeast-1'),
            'bucket' => env('AWS_PRIVATE_BUCKET'),
            'url' => env('AWS_PRIVATE_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'private',
            'throw' => false,
            'report' => false,
            'options' => [
                'ServerSideEncryption' => 'AES256', // Enkripsi server-side AWS
            ],
        ],
        */

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
