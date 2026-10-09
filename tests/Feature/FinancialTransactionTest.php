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

        $response->assertRedirect(route('income.index', ['month' => now()->month, 'year' => now()->year]));
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

        $response->assertRedirect(route('expenses.index', ['month' => now()->month, 'year' => now()->year]));
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::EXPENSE->value,
            'amount' => 350000.00,
            'description' => 'Beli SFP Transceiver 1.25G',
        ]);
    }

    public function test_authenticated_user_can_view_unified_transactions_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('transactions.index'));
        $response->assertOk();
        $response->assertSee('Buku Kas & Riwayat Transaksi');
        $response->assertSee('+ Catat Transaksi');
    }

    public function test_authenticated_user_can_record_unified_transaction_with_dropdown(): void
    {
        $user = User::factory()->create();
        $expenseCat = Category::create([
            'user_id' => $user->id,
            'type' => CategoryType::EXPENSE,
            'name' => 'Makanan & Minuman',
            'is_active' => true,
        ]);
        $incomeCat = Category::create([
            'user_id' => $user->id,
            'type' => CategoryType::INCOME,
            'name' => 'Bonus Proyek',
            'is_active' => true,
        ]);
        $pm = PaymentMethod::create([
            'user_id' => $user->id,
            'type' => PaymentMethodType::CASH,
            'name' => 'Tunai',
            'is_active' => true,
        ]);

        // 1. Record expense via unified endpoint
        $resExpense = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'expense',
            'amount' => '150.000',
            'category_id' => $expenseCat->id,
            'payment_method_id' => $pm->id,
            'transaction_date' => now()->toDateString(),
            'description' => 'Makan Siang Resto',
        ]);
        $resExpense->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::EXPENSE->value,
            'amount' => 150000.00,
            'description' => 'Makan Siang Resto',
        ]);

        // 2. Record income via unified endpoint
        $resIncome = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'income',
            'amount' => '2.500.000',
            'category_id' => $incomeCat->id,
            'payment_method_id' => $pm->id,
            'transaction_date' => now()->toDateString(),
            'description' => 'Bonus Proyek Selesai',
        ]);
        $resIncome->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::INCOME->value,
            'amount' => 2500000.00,
            'description' => 'Bonus Proyek Selesai',
        ]);
    }

    public function test_authenticated_user_can_update_unified_transaction(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'user_id' => $user->id,
            'type' => CategoryType::EXPENSE,
            'name' => 'Transportasi',
            'is_active' => true,
        ]);
        $tx = Transaction::create([
            'user_id' => $user->id,
            'type' => TransactionType::EXPENSE,
            'amount' => 50000.00,
            'category_id' => $category->id,
            'transaction_date' => now()->toDateString(),
            'description' => 'Bensin Awal',
        ]);

        $response = $this->actingAs($user)->put(route('transactions.update', $tx->id), [
            'type' => 'expense',
            'amount' => '75.000',
            'category_id' => $category->id,
            'transaction_date' => now()->toDateString(),
            'description' => 'Bensin Update Full Tank',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'id' => $tx->id,
            'amount' => 75000.00,
            'description' => 'Bensin Update Full Tank',
        ]);
    }

    public function test_authenticated_user_can_delete_unified_transaction(): void
    {
        $user = User::factory()->create();
        $tx = Transaction::create([
            'user_id' => $user->id,
            'type' => TransactionType::EXPENSE,
            'amount' => 30000.00,
            'transaction_date' => now()->toDateString(),
            'description' => 'Makan Bakso',
        ]);

        $response = $this->actingAs($user)->delete(route('transactions.destroy', $tx->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('transactions', [
            'id' => $tx->id,
        ]);
    }

    public function test_user_cannot_delete_other_user_transaction(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $tx = Transaction::create([
            'user_id' => $user1->id,
            'type' => TransactionType::EXPENSE,
            'amount' => 100000.00,
            'transaction_date' => now()->toDateString(),
            'description' => 'Transaksi User 1',
        ]);

        $response = $this->actingAs($user2)->delete(route('transactions.destroy', $tx->id));
        $response->assertForbidden();
        $this->assertDatabaseHas('transactions', [
            'id' => $tx->id,
        ]);
    }
}
