<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Tambah kolom id_member sebagai primary key yang bener

            // Verification code untuk dikirim ke user
            $table->string('verification_code', 6)->nullable()->after('status');

            // Timestamp kapan diverifikasi admin
            $table->timestamp('admin_verified_at')->nullable()->after('ktp_verified_at');

            // ID admin yang verifikasi
            $table->bigInteger('verified_by')->unsigned()->nullable()->after('admin_verified_at');

            // Tambah index
            $table->index('verification_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['id_member', 'verification_code', 'admin_verified_at', 'verified_by']);
        });
    }
};
