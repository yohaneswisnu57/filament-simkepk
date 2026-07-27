<?php

namespace Tests\Feature;

use App\Filament\Resources\Protocols\ProtocolResource;
use App\Models\Protocol;
use App\Models\Review;
use App\Models\ReviewerKelompok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ReviewerProtocolAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $reviewerNoGroup;

    protected User $reviewerInGroup;

    protected ReviewerKelompok $kelompok;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Setup Roles & Permissions
        $reviewerRole = Role::firstOrCreate(['name' => 'reviewer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $permissions = ['ViewAny:Protocol', 'View:Protocol'];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }
        $reviewerRole->syncPermissions($permissions);

        // Setup Reviewer Group
        $this->kelompok = ReviewerKelompok::create([
            'nama_kelompok' => 'Kelompok Test',
            'is_active' => 1,
        ]);

        // Reviewer without group
        $this->reviewerNoGroup = User::factory()->create([
            'reviewer_kelompok_id' => null,
        ]);
        $this->reviewerNoGroup->assignRole('reviewer');

        // Reviewer in group
        $this->reviewerInGroup = User::factory()->create([
            'reviewer_kelompok_id' => $this->kelompok->id,
        ]);
        $this->reviewerInGroup->assignRole('reviewer');
    }

    #[Test]
    public function it_allows_reviewer_with_null_group_to_access_directly_assigned_protocols(): void
    {
        // Create protocol
        $protocol = Protocol::factory()->create([
            'reviewer_kelompok_id' => null,
        ]);

        // Directly assign via pivot
        $protocol->reviewers()->attach($this->reviewerNoGroup->id, [
            'role_in_review' => 'Ketua',
            'feedback_status' => 'pending',
        ]);

        // Query using getEloquentQuery for reviewerNoGroup
        $this->actingAs($this->reviewerNoGroup);
        $queryResult = ProtocolResource::getEloquentQuery()->get();

        $this->assertTrue($queryResult->contains($protocol));
    }

    #[Test]
    public function it_allows_reviewer_in_group_to_access_protocols_assigned_to_group(): void
    {
        // Create protocol assigned to the kelompok
        $protocol = Protocol::factory()->create([
            'reviewer_kelompok_id' => $this->kelompok->id,
        ]);

        $this->actingAs($this->reviewerInGroup);
        $queryResult = ProtocolResource::getEloquentQuery()->get();

        $this->assertTrue($queryResult->contains($protocol));
    }

    #[Test]
    public function it_includes_group_assigned_protocols_in_pending_tasks_until_reviewed(): void
    {
        $user = $this->reviewerInGroup;

        // Create group protocol
        $protocol = Protocol::factory()->create([
            'reviewer_kelompok_id' => $this->kelompok->id,
        ]);

        // Pending count should be 1
        $pendingCount = Protocol::query()
            ->where(function ($query) use ($user) {
                $query->whereHas('reviewers', function ($q) use ($user) {
                    $q->where('users.id', $user->id)
                        ->where('feedback_status', 'pending');
                })
                    ->orWhere(function ($q) use ($user) {
                        if ($user->reviewer_kelompok_id) {
                            $q->where('reviewer_kelompok_id', $user->reviewer_kelompok_id)
                                ->whereDoesntHave('reviewers', function ($q2) use ($user) {
                                    $q2->where('users.id', $user->id);
                                })
                                ->whereDoesntHave('reviews', function ($q2) use ($user) {
                                    $q2->where('user_id', $user->id);
                                });
                        } else {
                            $q->whereRaw('1=0');
                        }
                    });
            })->count();

        $this->assertEquals(1, $pendingCount);

        // Reviewer submits a review
        Review::create([
            'protocol_id' => $protocol->id,
            'user_id' => $user->id,
            'comment' => 'Review comment',
            'verdict' => 'Exempted',
            'submitted_at' => now(),
        ]);

        // Pending count should now be 0
        $pendingCountAfter = Protocol::query()
            ->where(function ($query) use ($user) {
                $query->whereHas('reviewers', function ($q) use ($user) {
                    $q->where('users.id', $user->id)
                        ->where('feedback_status', 'pending');
                })
                    ->orWhere(function ($q) use ($user) {
                        if ($user->reviewer_kelompok_id) {
                            $q->where('reviewer_kelompok_id', $user->reviewer_kelompok_id)
                                ->whereDoesntHave('reviewers', function ($q2) use ($user) {
                                    $q2->where('users.id', $user->id);
                                })
                                ->whereDoesntHave('reviews', function ($q2) use ($user) {
                                    $q2->where('user_id', $user->id);
                                });
                        } else {
                            $q->whereRaw('1=0');
                        }
                    });
            })->count();

        $this->assertEquals(0, $pendingCountAfter);

        // Completed count should now be 1
        $completedCount = Protocol::query()
            ->where(function ($query) use ($user) {
                $query->whereHas('reviewers', function ($q) use ($user) {
                    $q->where('users.id', $user->id)
                        ->where('feedback_status', 'submitted');
                })
                    ->orWhere(function ($q) use ($user) {
                        if ($user->reviewer_kelompok_id) {
                            $q->where('reviewer_kelompok_id', $user->reviewer_kelompok_id)
                                ->whereDoesntHave('reviewers', function ($q2) use ($user) {
                                    $q2->where('users.id', $user->id);
                                })
                                ->whereHas('reviews', function ($q2) use ($user) {
                                    $q2->where('user_id', $user->id);
                                });
                        } else {
                            $q->whereRaw('1=0');
                        }
                    });
            })->count();

        $this->assertEquals(1, $completedCount);
    }

    #[Test]
    public function it_does_not_allow_reviewer_to_access_unassigned_protocols(): void
    {
        // Create protocol with no assignments
        $protocol = Protocol::factory()->create([
            'reviewer_kelompok_id' => null,
        ]);

        $this->actingAs($this->reviewerNoGroup);
        $queryResult = ProtocolResource::getEloquentQuery()->get();

        $this->assertFalse($queryResult->contains($protocol));
    }
}
