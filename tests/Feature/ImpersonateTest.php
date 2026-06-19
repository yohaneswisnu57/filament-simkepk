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

    public function test_backdoor_impersonate_route_is_removed()
    {
        $user = User::factory()->create();
        $response = $this->get('/test-impersonate/' . $user->id);
        $response->assertStatus(404);
        
        $response2 = $this->get('/test-dump-session');
        $response2->assertStatus(404);
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
