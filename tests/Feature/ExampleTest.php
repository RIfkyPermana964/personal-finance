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

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_root_redirects_unauthenticated_user_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_dashboard_renders_successfully_for_authenticated_user_with_transactions(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'user_id' => $user->id,
            'type' => CategoryType::EXPENSE,
            'name' => 'Makan',
            'is_active' => true,
        ]);
        $paymentMethod = PaymentMethod::create([
            'user_id' => $user->id,
            'type' => PaymentMethodType::CASH,
            'name' => 'Tunai',
            'is_active' => true,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
            'amount' => 50000,
            'type' => TransactionType::EXPENSE,
            'transaction_date' => now()->toDateString(),
            'description' => 'Makan Siang',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Keuangan Pribadi');
        $response->assertSee('Total Uang Masuk');
        $response->assertSee('Total Uang Keluar');
        $response->assertSee('Makan Siang');
    }
}
