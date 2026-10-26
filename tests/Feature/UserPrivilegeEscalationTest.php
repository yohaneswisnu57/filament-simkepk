<?php

namespace Tests\Feature;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserPrivilegeEscalationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        foreach (['Update', 'Delete', 'Restore', 'ForceDelete', 'Replicate'] as $action) {
            Permission::firstOrCreate(['name' => "{$action}:User", 'guard_name' => 'web']);
        }
    }

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $admin->givePermissionTo(Permission::all());

        return $admin;
    }

    #[Test]
    public function admin_cannot_manage_a_super_admin(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $policy = new UserPolicy;
        $admin = $this->makeAdmin();

        $this->assertFalse($policy->update($admin, $superAdmin));
        $this->assertFalse($policy->delete($admin, $superAdmin));
        $this->assertFalse($policy->restore($admin, $superAdmin));
        $this->assertFalse($policy->forceDelete($admin, $superAdmin));
        $this->assertFalse($policy->replicate($admin, $superAdmin));
    }

    #[Test]
    public function admin_can_still_manage_regular_users(): void
    {
        $regular = User::factory()->create();

        $this->assertTrue((new UserPolicy)->update($this->makeAdmin(), $regular));
    }

    #[Test]
    public function super_admin_can_manage_another_super_admin(): void
    {
        $target = User::factory()->create();
        $target->assignRole('super_admin');

        $actor = User::factory()->create();
        $actor->assignRole('super_admin');
        $actor->givePermissionTo(Permission::all());

        $this->assertTrue((new UserPolicy)->update($actor, $target));
    }
}
