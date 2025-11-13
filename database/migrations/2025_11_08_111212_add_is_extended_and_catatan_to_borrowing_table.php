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
        Schema::table('borrowing', function (Blueprint $table) {
            // Ubah status jadi VARCHAR (lebih fleksibel)
            if (Schema::hasColumn('borrowing', 'status')) {
                $table->string('status', 50)->default('Dipinjam')->change();
            }

            // Tambah kolom is_extended (jika belum ada)
            if (!Schema::hasColumn('borrowing', 'is_extended')) {
                $table->boolean('is_extended')->default(false)->after('status');
            }

            // Tambah kolom catatan (untuk catatan pengembalian)
            if (!Schema::hasColumn('borrowing', 'catatan')) {
                $table->text('catatan')->nullable()->after('kondisi');
            }

            // Tambah soft delete (jika belum ada)
            if (!Schema::hasColumn('borrowing', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowing', function (Blueprint $table) {
            // Rollback ke ENUM (sesuaikan value dengan database asli)
            // $table->enum('status', ['pending', 'Dipinjam', 'Dikembalikan'])->change();

            $table->dropColumn(['is_extended', 'catatan', 'deleted_at']);
        });
    }
};
