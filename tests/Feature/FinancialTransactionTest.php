<?php

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Enums\PaymentMethodType;
use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_record_income(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'user_id' => $user->id,
            'type' => CategoryType::INCOME,
            'name' => 'Gaji Pokok NOC',
            'is_active' => true,
        ]);
        $paymentMethod = PaymentMethod::create([
            'user_id' => $user->id,
            'type' => PaymentMethodType::BANK,
            'name' => 'Bank BCA',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/income', [
            'type' => 'income',
            'amount' => 7500000.00,
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
            'transaction_date' => now()->toDateString(),
            'description' => 'Gaji Pokok NOC Test',
        ]);

        $response->assertRedirect('/income');
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::INCOME->value,
            'amount' => 7500000.00,
            'description' => 'Gaji Pokok NOC Test',
        ]);
    }

    public function test_authenticated_user_can_record_expense(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'user_id' => $user->id,
            'type' => CategoryType::EXPENSE,
            'name' => 'Alat Fiber Optic & SFP',
            'is_active' => true,
        ]);
        $paymentMethod = PaymentMethod::create([
            'user_id' => $user->id,
            'type' => PaymentMethodType::BANK,
            'name' => 'Bank Mandiri',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/expenses', [
            'type' => 'expense',
            'amount' => 350000.00,
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
            'transaction_date' => now()->toDateString(),
            'description' => 'Beli SFP Transceiver 1.25G',
        ]);

        $response->assertRedirect('/expenses');
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::EXPENSE->value,
            'amount' => 350000.00,
            'description' => 'Beli SFP Transceiver 1.25G',
        ]);
    }
}