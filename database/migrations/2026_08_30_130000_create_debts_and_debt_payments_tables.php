<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->default('debt'); // receivable, debt, rent
            $table->string('name'); // Nama Peminjam / Nama Kreditur / Item Sewa
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2);
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('unpaid'); // unpaid, nyicil, paid
            $table->string('rent_type')->nullable(); // misal: Kosan, Kontrakan, Server, dll jika tipe rent
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['debt_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
        Schema::dropIfExists('debts');
    }
};
