<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('ktp_number_enc')->nullable()->after('email'); // ganti posisi setelah 'email'
            $table->string('ktp_hash', 64)->nullable()->after('ktp_number_enc');
            $table->string('ktp_photo_path')->nullable()->after('ktp_hash');
            $table->string('photo_path')->nullable()->after('ktp_photo_path');
        });

    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ktp_number_enc','ktp_hash','ktp_photo_path','photo_path']);
        });
    }
};
