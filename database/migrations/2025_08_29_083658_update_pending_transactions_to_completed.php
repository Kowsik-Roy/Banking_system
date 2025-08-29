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
        // Update all existing pending transactions to completed
        // Since these transactions were successful (money was transferred), they should be marked as completed
        DB::table('transactions')
            ->where('status', 'pending')
            ->update(['status' => 'completed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to pending (though this might not be accurate)
        DB::table('transactions')
            ->where('status', 'completed')
            ->update(['status' => 'pending']);
    }
};
