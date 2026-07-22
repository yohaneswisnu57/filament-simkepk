<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (['super_admin', 'admin', 'sekertaris', 'Ketua Reviewer', 'reviewer', 'panel_reviewer', 'user', 'panel_user'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    #[Test]
    public function super_admin_can_access_admin_panel(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('super_admin');

        $this->assertTrue($user->canAccessPanel(Filament::getPanel('admin')));
    }

    #[Test]
    public function inactive_user_cannot_access_any_panel(): void
    {
        $user = User::factory()->create(['is_active' => false]);
        $user->assignRole('super_admin');

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('admin')));
    }
}
