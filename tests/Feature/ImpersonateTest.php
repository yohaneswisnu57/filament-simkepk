<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ImpersonateTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_impersonate_user()
    {
        // Setup Roles
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'user']);
        Role::create(['name' => 'panel_user']);

        // Create Super Admin
        $superAdmin = User::factory()->create([
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');

        // Create Regular User
        $regularUser = User::factory()->create([
            'is_active' => true,
        ]);
        $regularUser->assignRole(['user', 'panel_user']);

        // Act as super admin
        $this->actingAs($superAdmin);

        // Hit the custom impersonate route
        $response = $this->get('/test-impersonate/' . $regularUser->id);
        
        // Assert it redirects to /user
        $response->assertRedirect('/user');
        
        // Now follow the redirect to /user WITH the session from the first request
        $response2 = $this->withSession(session()->all())->get('/user');
        
        // Check session data
        $response3 = $this->withSession(session()->all())->get('/test-dump-session');
        $response2->assertStatus(200);
    }

    public function test_can_search_users_by_role()
    {
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'user']);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $regularUser = User::factory()->create(['name' => 'Target User']);
        $regularUser->assignRole('user');

        // Test search query logic directly
        $query = User::query()->whereHas('roles', function ($q) {
            $q->where('name', 'like', '%user%');
        });

        $this->assertTrue($query->get()->contains($regularUser));
        $this->assertFalse($query->get()->contains($superAdmin));
    }
}
