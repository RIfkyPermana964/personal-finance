<?php

namespace Tests\Feature;

use App\Enums\DebtStatus;
use App\Enums\DebtType;
use App\Enums\TransactionType;
use App\Models\Debt;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebtTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_debts_index(): void
    {
        $user = User::factory()->create();

        Debt::create([
            'user_id' => $user->id,
            'type' => DebtType::RECEIVABLE,
            'name' => 'Budi Santoso',
            'total_amount' => 1500000.00,
            'paid_amount' => 0.00,
            'remaining_amount' => 1500000.00,
            'status' => DebtStatus::UNPAID,
        ]);

        $response = $this->actingAs($user)->get('/debts');
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Piutang');

        // Test filtering
        $responseReceivable = $this->actingAs($user)->get('/debts?type=receivable');
        $responseReceivable->assertStatus(200);
        $responseReceivable->assertSee('Budi Santoso');

        $responseDebt = $this->actingAs($user)->get('/debts?type=debt');
        $responseDebt->assertStatus(200);
    }

    public function test_user_can_create_receivable_with_formatted_thousands_amount(): void
    {
        $user = User::factory()->create();

        // Testing amount with thousand separators: "2.500.000"
        $response = $this->actingAs($user)->post('/debts', [
            'type' => 'receivable',
            'name' => 'Doni Saputra',
            'total_amount' => '2.500.000',
            'start_date' => now()->toDateString(),
            'due_date' => now()->addMonth()->toDateString(),
            'notes' => 'Pinjam untuk biaya kuliah',
        ]);

        $response->assertRedirect('/debts?type=receivable');
        $this->assertDatabaseHas('debts', [
            'user_id' => $user->id,
            'type' => 'receivable',
            'name' => 'Doni Saputra',
            'total_amount' => 2500000.00,
            'paid_amount' => 0.00,
            'remaining_amount' => 2500000.00,
            'status' => 'unpaid',
        ]);
    }

    public function test_user_can_create_debt_and_rent(): void
    {
        $user = User::factory()->create();

        // 1. Debt (Pinjaman kita sendiri)
        $this->actingAs($user)->post('/debts', [
            'type' => 'debt',
            'name' => 'Bank Mandiri KTA',
            'total_amount' => '10.000.000',
            'start_date' => now()->toDateString(),
            'due_date' => now()->addYear()->toDateString(),
            'notes' => 'Pinjaman modal usaha',
        ]);

        $this->assertDatabaseHas('debts', [
            'user_id' => $user->id,
            'type' => 'debt',
            'name' => 'Bank Mandiri KTA',
            'total_amount' => 10000000.00,
        ]);

        // 2. Rent (Sewa Kos / Kontrakan)
        $this->actingAs($user)->post('/debts', [
            'type' => 'rent',
            'name' => 'Kost Exclusive Menteng',
            'rent_type' => 'Kost Bulanan',
            'total_amount' => '1.800.000',
            'due_date' => now()->endOfMonth()->toDateString(),
        ]);

        $this->assertDatabaseHas('debts', [
            'user_id' => $user->id,
            'type' => 'rent',
            'name' => 'Kost Exclusive Menteng',
            'rent_type' => 'Kost Bulanan',
            'total_amount' => 1800000.00,
        ]);
    }

    public function test_user_can_record_payment_and_status_transitions(): void
    {
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::create([
            'user_id' => $user->id,
            'type' => 'bank',
            'name' => 'BCA',
            'is_active' => true,
        ]);

        $debt = Debt::create([
            'user_id' => $user->id,
            'type' => DebtType::DEBT,
            'name' => 'Pinjaman Teman',
            'total_amount' => 1000000.00,
            'paid_amount' => 0.00,
            'remaining_amount' => 1000000.00,
            'status' => DebtStatus::UNPAID,
        ]);

        // 1. Cicilan pertama: 400.000
        $this->actingAs($user)->post("/debts/{$debt->id}/payment", [
            'amount' => '400.000',
            'payment_date' => now()->toDateString(),
            'payment_method_id' => $paymentMethod->id,
            'notes' => 'Cicilan 1',
        ]);

        $debt->refresh();
        $this->assertEquals(400000.00, $debt->paid_amount);
        $this->assertEquals(600000.00, $debt->remaining_amount);
        $this->assertEquals(DebtStatus::NYICIL, $debt->status);
        $this->assertCount(1, $debt->payments);

        // 2. Pelunasan sisa: 600.000
        $this->actingAs($user)->post("/debts/{$debt->id}/payment", [
            'amount' => '600.000',
            'payment_date' => now()->toDateString(),
            'payment_method_id' => $paymentMethod->id,
            'notes' => 'Pelunasan sisa',
        ]);

        $debt->refresh();
        $this->assertEquals(1000000.00, $debt->paid_amount);
        $this->assertEquals(0.00, $debt->remaining_amount);
        $this->assertEquals(DebtStatus::PAID, $debt->status);
    }

    public function test_recording_payment_can_sync_to_transactions(): void
    {
        $user = User::factory()->create();
        $paymentMethod = PaymentMethod::create([
            'user_id' => $user->id,
            'type' => 'bank',
            'name' => 'BCA',
            'is_active' => true,
        ]);

        // Piutang (orang bayar ke kita -> harusnya tercatat sebagai pemasukan / INCOME)
        $receivable = Debt::create([
            'user_id' => $user->id,
            'type' => DebtType::RECEIVABLE,
            'name' => 'Teman Kantor',
            'total_amount' => 500000.00,
            'paid_amount' => 0.00,
            'remaining_amount' => 500000.00,
            'status' => DebtStatus::UNPAID,
        ]);

        $this->actingAs($user)->post("/debts/{$receivable->id}/payment", [
            'amount' => '500.000',
            'payment_date' => now()->toDateString(),
            'payment_method_id' => $paymentMethod->id,
            'record_as_transaction' => '1',
            'notes' => 'Lunas via transfer',
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::INCOME->value,
            'amount' => 500000.00,
            'payment_method_id' => $paymentMethod->id,
        ]);
    }

    public function test_user_cannot_modify_other_users_debt(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $debt = Debt::create([
            'user_id' => $user1->id,
            'type' => DebtType::DEBT,
            'name' => 'Hutang User 1',
            'total_amount' => 1000000.00,
            'paid_amount' => 0.00,
            'remaining_amount' => 1000000.00,
            'status' => DebtStatus::UNPAID,
        ]);

        // User 2 tries to update
        $response = $this->actingAs($user2)->put("/debts/{$debt->id}", [
            'type' => 'debt',
            'name' => 'Hacker Name',
            'total_amount' => '2.000.000',
        ]);
        $response->assertStatus(403);

        // User 2 tries to delete
        $responseDelete = $this->actingAs($user2)->delete("/debts/{$debt->id}");
        $responseDelete->assertStatus(403);
    }
}
