<?php
// database/migrations/2024_01_01_000000_create_members_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('alamat');
            $table->string('no_telp', 20);

            // KTP - Encrypted data (text untuk ciphertext panjang)
            $table->text('ktp_number_enc')->nullable();

            // KTP - Hash untuk deduplikasi (tidak bisa decrypt)
            $table->string('ktp_hash', 64)->nullable()->unique();

            // File paths (relative ke storage/app/private)
            $table->string('ktp_photo_path')->nullable();
            $table->string('photo_path')->nullable();

            // Verification & status
            $table->timestamp('ktp_verified_at')->nullable();
            $table->string('role', 20)->default('member'); // member, admin
            $table->string('status', 20)->default('active'); // active, inactive, suspended

            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk audit trail

            // Indexes
            $table->index('ktp_hash'); // Fast lookup untuk duplikasi
            $table->index(['role', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
