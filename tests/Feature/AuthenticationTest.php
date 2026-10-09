<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Personal Financial System');
        $response->assertSee('Lanjutkan dengan Akun Google');
        $response->assertSee('Daftar Sekarang');
    }

    public function test_users_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'noc@isp.net',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'noc@isp.net',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'noc@isp.net',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'email' => 'noc@isp.net',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Baru');
        $response->assertSee('Daftar Instan dengan Akun Google');
    }

    public function test_user_can_register_manually(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe Financial',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe Financial',
            'email' => 'john.doe@example.com',
            'currency' => 'IDR',
        ]);

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_google_redirect_shows_dev_screen_when_credentials_not_set_in_local(): void
    {
        Config::set('services.google.client_id', null);
        Config::set('services.google.client_secret', null);
        $this->app['env'] = 'local';

        $response = $this->get(route('auth.google'));
        $response->assertOk();
        $response->assertSee('Koneksi Akun Google');
    }

    public function test_dev_login_authenticates_simulated_google_user(): void
    {
        $response = $this->withoutMiddleware(ValidateCsrfToken::class)
            ->post(route('auth.google.dev'), [
                'email' => 'budi.finance@gmail.com',
                'name' => 'Budi Santoso',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'budi.finance@gmail.com',
            'name' => 'Budi Santoso',
        ]);

        $user = User::where('email', 'budi.finance@gmail.com')->first();
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_google_callback_creates_and_authenticates_new_user(): void
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-uid-999');
        $abstractUser->shouldReceive('getName')->andReturn('Siti Google User');
        $abstractUser->shouldReceive('getEmail')->andReturn('siti@gmail.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/siti-avatar.png');

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get(route('auth.google.callback'));

        $this->assertDatabaseHas('users', [
            'name' => 'Siti Google User',
            'email' => 'siti@gmail.com',
            'google_id' => 'google-uid-999',
            'avatar' => 'https://lh3.googleusercontent.com/siti-avatar.png',
        ]);

        $user = User::where('email', 'siti@gmail.com')->first();
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_google_callback_links_existing_user_by_email(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@gmail.com',
            'google_id' => null,
            'avatar' => null,
        ]);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-uid-888');
        $abstractUser->shouldReceive('getName')->andReturn('Existing User Google');
        $abstractUser->shouldReceive('getEmail')->andReturn('existing@gmail.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/new-avatar.png');

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get(route('auth.google.callback'));

        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'email' => 'existing@gmail.com',
            'google_id' => 'google-uid-888',
            'avatar' => 'https://lh3.googleusercontent.com/new-avatar.png',
        ]);

        $this->assertAuthenticatedAs($existingUser);
        $response->assertRedirect(route('dashboard'));
    }
}
