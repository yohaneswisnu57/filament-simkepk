<?php

namespace Tests\Feature;

use App\Models\Protocol;
use App\Models\ReviewerKelompok;
use App\Models\StatusReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FastReviewAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup Roles
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sekertaris', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'reviewer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
    }

    /** @test */
    public function it_assigns_chairman_and_secretary_when_status_is_fast_review()
    {
        // 1. Setup Data
        $statusDraft = StatusReview::create(['status_name' => 'Draft']);
        $statusFastReview = StatusReview::create(['status_name' => 'Fast Review']);
        
        $chairman = User::factory()->create();
        $secretary = User::factory()->create();
        $secretary->assignRole('sekertaris');
        
        $member = User::factory()->create();
        
        $group = ReviewerKelompok::create([
            'nama_kelompok' => 'Group A',
            'ketua_user_id' => $chairman->id,
            'is_active' => true
        ]);

        
        $member->reviewer_kelompok_id = $group->id;
        $member->save();

        $protocol = Protocol::factory()->create([
            'reviewer_kelompok_id' => $group->id,
            'status_id' => $statusDraft->id // Start with Draft
        ]);

        // 2. Act: Change status to Fast Review
        $protocol->update(['status_id' => $statusFastReview->id]);

        // 3. Assert
        $this->assertDatabaseHas('protocol_reviewers', [
            'protocol_id' => $protocol->id,
            'user_id' => $chairman->id,
            'role_in_review' => 'Ketua'
        ]);

        $this->assertDatabaseHas('protocol_reviewers', [
            'protocol_id' => $protocol->id,
            'user_id' => $secretary->id,
            'role_in_review' => 'Sekertaris'
        ]);

        // Member should NOT be in protocol_reviewers
        $this->assertDatabaseMissing('protocol_reviewers', [
            'protocol_id' => $protocol->id,
            'user_id' => $member->id
        ]);
        
        // Ensure Pivot Count is 2
        $this->assertEquals(2, $protocol->reviewers()->count());
    }
}
