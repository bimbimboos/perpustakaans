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
            // Add denda column if it doesn't exist
            if (!Schema::hasColumn('borrowing', 'denda')) {
                $table->decimal('denda', 10, 2)->nullable()->after('catatan')->comment('Denda keterlambatan atau kerusakan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowing', function (Blueprint $table) {
            if (Schema::hasColumn('borrowing', 'denda')) {
                $table->dropColumn('denda');
            }
        });
    }
};
