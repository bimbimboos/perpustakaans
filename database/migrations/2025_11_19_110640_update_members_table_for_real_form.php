<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {


            // Tambah kolom baru sesuai formulir

            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])->nullable()->after('tanggal_lahir');
            $table->string('institusi')->nullable()->after('agama'); // Sekolah/Univ/Kantor
            $table->text('alamat_institusi')->nullable()->after('institusi');
            $table->enum('jenjang_pendidikan', ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3', 'Umum'])->nullable()->after('alamat_institusi');
            $table->string('no_hp_ortu')->nullable()->after('no_telp');
            $table->year('tahun_pembuatan')->nullable()->after('verification_code');
        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('password')->nullable();
            $table->dropColumn([

                'tanggal_lahir',
                'agama',
                'institusi',
                'alamat_institusi',
                'jenjang_pendidikan',
                'no_hp_ortu',
                'tahun_pembuatan',
            ]);
        });
    }
};
