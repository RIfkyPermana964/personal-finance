<?php

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_set_monthly_budget(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'user_id' => $user->id,
            'type' => CategoryType::EXPENSE,
            'name' => 'Makan & Minum',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/budgets', [
            'category_id' => $category->id,
            'month' => 8,
            'year' => 2026,
            'amount' => 1200000.00,
        ]);

        $response->assertRedirect('/budgets?month=8&year=2026');
        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 8,
            'year' => 2026,
            'amount' => 1200000.00,
        ]);
    }
}
