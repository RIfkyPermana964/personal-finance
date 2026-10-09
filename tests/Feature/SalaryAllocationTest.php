<?php

namespace Tests\Feature;

use App\Models\SalaryAllocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalaryAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_salary_allocation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/salary-allocations', [
            'month' => 8,
            'year' => 2026,
            'item_name' => 'Sewa Kosan',
            'amount' => 1200000.00,
            'status' => 'unpaid',
        ]);

        $response->assertRedirect('/salary-allocations?month=8&year=2026');
        $this->assertDatabaseHas('salary_allocations', [
            'user_id' => $user->id,
            'item_name' => 'Sewa Kosan',
            'amount' => 1200000.00,
            'status' => 'unpaid',
        ]);
    }

    public function test_user_can_toggle_allocation_paid_status(): void
    {
        $user = User::factory()->create();
        $allocation = SalaryAllocation::create([
            'user_id' => $user->id,
            'month' => 8,
            'year' => 2026,
            'item_name' => 'Token Listrik',
            'amount' => 300000.00,
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($user)->patch("/salary-allocations/{$allocation->id}/toggle");

        $allocation->refresh();
        $this->assertEquals('paid', $allocation->status);
        $this->assertNotNull($allocation->paid_date);

        // Toggle back
        $this->actingAs($user)->patch("/salary-allocations/{$allocation->id}/toggle");
        $allocation->refresh();
        $this->assertEquals('unpaid', $allocation->status);
    }
}
