<?php

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_category_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/categories');
        $response->assertOk();
    }

    public function test_user_can_reorder_categories(): void
    {
        $user = User::factory()->create();

        $cat1 = Category::create([
            'user_id' => $user->id,
            'type' => CategoryType::EXPENSE,
            'name' => 'Kategori 1',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $cat2 = Category::create([
            'user_id' => $user->id,
            'type' => CategoryType::EXPENSE,
            'name' => 'Kategori 2',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/categories/reorder', [
            'items' => [
                ['id' => $cat1->id, 'sort_order' => 2],
                ['id' => $cat2->id, 'sort_order' => 1],
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertEquals(2, $cat1->fresh()->sort_order);
        $this->assertEquals(1, $cat2->fresh()->sort_order);
    }
}
