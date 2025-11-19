<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change primary key to varchar untuk support format TRX
        Schema::table('borrowing', function (Blueprint $table) {
            // Drop auto increment first
            $table->string('id_peminjaman', 20)->change();
        });

        // Generate transaction IDs untuk data yang ada
        $this->generateTransactionIds();
    }

    /**
     * Generate transaction IDs dengan format TRXYYYYMMDDnnnn
     */
    private function generateTransactionIds()
    {
        $borrowings = DB::table('borrowing')
            ->orderBy('pinjam', 'asc')
            ->get();

        $grouped = $borrowings->groupBy(function($item) {
            return date('Ymd', strtotime($item->pinjam));
        });

        foreach ($grouped as $date => $group) {
            $counter = 1;
            foreach ($group as $borrowing) {
                $newId = 'TRX' . $date . str_pad($counter, 4, '0', STR_PAD_LEFT);

                DB::table('borrowing')
                    ->where('id_peminjaman', $borrowing->id_peminjaman)
                    ->update(['id_peminjaman' => $newId]);

                $counter++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak bisa reverse karena sudah ubah format ID
        // Backup database sebelum migrate!
    }
};
