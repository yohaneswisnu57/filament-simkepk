<?php

namespace Tests\Feature;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteTwoUser;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
    }

    #[Test]
    public function new_google_user_is_auto_created_and_logged_in(): void
    {
        Socialite::fake('google', (new SocialiteTwoUser)->map([
            'id' => 'google-123',
            'name' => 'Jane Doe',
            'email' => 'newuser@example.com',
        ]));

        $response = $this->get('/auth/google/callback');

        $user = User::where('email', 'newuser@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('google-123', $user->google_id);
        $this->assertSame('google', $user->provider);
        $this->assertTrue($user->hasRole('user'));
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/user');
    }

    #[Test]
    public function existing_user_with_matching_email_is_auto_linked(): void
    {
        $existing = User::factory()->create(['email' => 'linked@example.com']);

        Socialite::fake('google', (new SocialiteTwoUser)->map([
            'id' => 'google-456',
            'name' => 'Linked User',
            'email' => 'linked@example.com',
        ]));

        $response = $this->get('/auth/google/callback');

        $existing->refresh();

        $this->assertSame('google-456', $existing->google_id);
        $this->assertSame('google', $existing->provider);
        $this->assertAuthenticatedAs($existing);
        $response->assertRedirect('/user');

        $this->assertSame(1, User::where('email', 'linked@example.com')->count());
    }

    #[Test]
    public function inactive_user_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'is_active' => false,
        ]);

        Socialite::fake('google', (new SocialiteTwoUser)->map([
            'id' => 'google-789',
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
        ]));

        $response = $this->get('/auth/google/callback');

        $this->assertGuest();
        $response->assertRedirect(route('filament.user.auth.login'));
    }

    #[Test]
    public function callback_failure_redirects_to_login_with_error(): void
    {
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturnSelf();

        Socialite::shouldReceive('user')
            ->andThrow(new Exception('invalid_state'));

        $response = $this->get('/auth/google/callback');

        $this->assertGuest();
        $response->assertRedirect(route('filament.user.auth.login'));
        $response->assertSessionHasErrors('email');
    }
}
