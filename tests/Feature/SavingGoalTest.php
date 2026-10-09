<?php

namespace Tests\Feature;

use App\Enums\SavingGoalStatus;
use App\Models\SavingGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingGoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_saving_goal(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/saving-goals', [
            'name' => 'Dana Darurat NOC',
            'target_amount' => 10000000.00,
            'target_date' => now()->addMonths(6)->toDateString(),
            'notes' => 'Simpan di RDPU',
        ]);

        $response->assertRedirect('/saving-goals');
        $this->assertDatabaseHas('saving_goals', [
            'user_id' => $user->id,
            'name' => 'Dana Darurat NOC',
            'target_amount' => 10000000.00,
        ]);
    }

    public function test_user_can_deposit_and_withdraw_saving_goal(): void
    {
        $user = User::factory()->create();
        $goal = SavingGoal::create([
            'user_id' => $user->id,
            'name' => 'Sertifikasi MTCNA',
            'target_amount' => 3000000.00,
            'current_amount' => 0.00,
            'status' => SavingGoalStatus::ACTIVE,
        ]);

        // Deposit
        $this->actingAs($user)->post("/saving-goals/{$goal->id}/mutate", [
            'type' => 'deposit',
            'amount' => 1500000.00,
            'transaction_date' => now()->toDateString(),
            'notes' => 'Alokasi Gaji 1',
        ]);

        $goal->refresh();
        $this->assertEquals(1500000.00, $goal->current_amount);
        $this->assertEquals(50.0, $goal->progress_percentage);

        // Withdraw
        $this->actingAs($user)->post("/saving-goals/{$goal->id}/mutate", [
            'type' => 'withdraw',
            'amount' => 500000.00,
            'transaction_date' => now()->toDateString(),
            'notes' => 'Bayar pendaftaran awal',
        ]);

        $goal->refresh();
        $this->assertEquals(1000000.00, $goal->current_amount);
    }
}
