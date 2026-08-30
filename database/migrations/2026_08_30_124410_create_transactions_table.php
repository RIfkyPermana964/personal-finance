<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['income', 'expense', 'saving_deposit', 'saving_withdraw']);
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->foreignId('saving_goal_id')->nullable()->constrained('saving_goals')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('description', 255)->nullable();
            $table->string('receipt_image', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'transaction_date']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'category_id']);
            $table->index('saving_goal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
