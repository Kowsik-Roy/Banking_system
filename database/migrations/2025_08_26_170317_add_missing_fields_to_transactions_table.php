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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'shared_key')) {
                $table->text('shared_key')->nullable(); // Diffie-Hellman shared secret
            }
            if (!Schema::hasColumn('transactions', 'encrypted_payload')) {
                $table->text('encrypted_payload')->nullable(); // Encrypted transaction data
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['shared_key', 'encrypted_payload']);
        });
    }
};
