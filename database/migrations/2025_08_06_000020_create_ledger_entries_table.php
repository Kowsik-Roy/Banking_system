<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('entry_type', ['debit','credit']);
            $table->decimal('amount', 12, 2);
            $table->string('prev_hash')->nullable();   // for tamper detection (hash chain)
            $table->string('hash');                    // hash(current row + prev_hash)
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ledger_entries');
    }
};
