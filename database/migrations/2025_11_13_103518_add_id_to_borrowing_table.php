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
        Schema::table('borrowing', function (Blueprint $table) {
            // Add transaction_id column
            $table->string('transaction_id', 20)->nullable()->after('id_peminjaman');
            $table->index('transaction_id');
        });

        // Generate transaction_id untuk data yang sudah ada
        $this->generateTransactionIds();
    }

    /**
     * Generate transaction IDs for existing records
     */
    private function generateTransactionIds()
    {
        // Group by user and date, then assign same transaction_id
        $borrowings = DB::table('borrowing')
            ->whereNull('transaction_id')
            ->orderBy('pinjam', 'asc')
            ->get();

        $grouped = $borrowings->groupBy(function($item) {
            return $item->id_user . '_' . date('Y-m-d', strtotime($item->pinjam));
        });

        $counter = 1;
        foreach ($grouped as $group) {
            $transactionId = 'TRX' . date('Ymd') . str_pad($counter, 4, '0', STR_PAD_LEFT);

            foreach ($group as $borrowing) {
                DB::table('borrowing')
                    ->where('id_peminjaman', $borrowing->id_peminjaman)
                    ->update(['transaction_id' => $transactionId]);
            }

            $counter++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowing', function (Blueprint $table) {
            $table->dropIndex(['transaction_id']);
            $table->dropColumn('transaction_id');
        });
    }
};
