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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('kpp_tx_reference')->unique();
            $table->string('status');
            $table->string('type')->nullable(); // debit/credit
            $table->string('method')->nullable(); // mobile_money, card, etc.
            $table->decimal('amount', 10, 2);
            $table->decimal('fees', 10, 2)->default(0);
            $table->string('currency', 3)->default('XOF');
            $table->text('description')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
