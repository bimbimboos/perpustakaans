<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $table) {
                $table->id('id_member');
                $table->string('nama');
                $table->string('email')->unique();
                $table->string('no_hp')->nullable();
                $table->text('alamat')->nullable();
                $table->text('ktp_number_enc')->nullable();
                $table->string('ktp_hash', 64)->nullable();
                $table->string('ktp_photo_path')->nullable();
                $table->string('photo_path')->nullable();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
