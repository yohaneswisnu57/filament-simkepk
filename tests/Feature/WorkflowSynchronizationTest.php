<?php

namespace Tests\Feature;

use App\Filament\Resources\Protocols\Pages\EditProtocol;
use App\Models\Protocol;
use App\Models\ReviewerKelompok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkflowSynchronizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        // Setup Roles
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sekertaris', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'reviewer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Seed Status Reviews
        \DB::table('status_reviews')->insert([
            ['id' => 1, 'status_name' => 'EXEMPTED'],
            ['id' => 2, 'status_name' => 'FULL BOARD'],
            ['id' => 3, 'status_name' => 'EXPEDITED'],
            ['id' => 4, 'status_name' => 'ON REVIEW'],
            ['id' => 5, 'status_name' => 'CERTIFICATE'],
            ['id' => 6, 'status_name' => 'FAST REVIEW'],
            ['id' => 7, 'status_name' => 'SUBMISSION'],
        ]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** @test */
    public function it_synchronizes_fast_review_decision_when_status_id_is_updated()
    {
        $protocol = Protocol::factory()->create([
            'status_id' => 7, // Submission
            'fast_review_decision' => 'Pending',
        ]);

        // Change status to Exempted (1)
        $protocol->update(['status_id' => 1]);

        $this->assertEquals('Exempted', $protocol->fresh()->fast_review_decision);

        // Change status to Expedited (3)
        $protocol->update(['status_id' => 3]);

        $this->assertEquals('Expedited', $protocol->fresh()->fast_review_decision);
    }

    /** @test */
    public function it_sends_notification_to_group_members_on_regular_assignment()
    {
        $kelompok = ReviewerKelompok::create(['nama_kelompok' => 'Group A', 'is_active' => true]);
        
        $reviewer = User::factory()->create(['reviewer_kelompok_id' => $kelompok->id]);
        $reviewer->assignRole('reviewer');

        $protocol = Protocol::factory()->create(['status_id' => 7]);

        // Assign group with status ON REVIEW (4) - NOT Fast Review
        $protocol->update([
            'reviewer_kelompok_id' => $kelompok->id,
            'status_id' => 4,
        ]);

        $this->assertCount(1, $reviewer->notifications);
        $this->assertStringContainsString('assigned Group "Group A"', $reviewer->notifications->first()->data['body']);
    }

    /** @test */
    public function it_skips_group_notification_on_fast_review_assignment()
    {
        $kelompok = ReviewerKelompok::create(['nama_kelompok' => 'Group A', 'is_active' => true]);
        
        $reviewer = User::factory()->create(['reviewer_kelompok_id' => $kelompok->id]);
        $reviewer->assignRole('reviewer');

        $protocol = Protocol::factory()->create(['status_id' => 7]);

        // Assign group with status FAST REVIEW (6)
        $protocol->update([
            'reviewer_kelompok_id' => $kelompok->id,
            'status_id' => 6,
        ]);

        // Group notification should be skipped
        $this->assertCount(0, $reviewer->notifications);
    }

    /** @test */
    public function it_sends_individual_notifications_on_fast_review_assignment_via_edit_page()
    {
        $kelompok = ReviewerKelompok::create(['nama_kelompok' => 'Group A', 'is_active' => true]);
        
        $ketua = User::factory()->create();
        $ketua->assignRole('reviewer');

        $sekertaris = User::factory()->create();
        $sekertaris->assignRole('reviewer');

        $protocol = Protocol::factory()->create([
            'status_id' => 6,
            'reviewer_kelompok_id' => $kelompok->id
        ]);

        $this->actingAs($this->admin);

        // Simulate Filament Edit Page Save
        Livewire::test(EditProtocol::class, ['record' => $protocol->getKey()])
            ->fillForm([
                'fast_review_ketua_id' => $ketua->id,
                'fast_review_secretary_id' => $sekertaris->id,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        // Verify individual notifications
        $this->assertCount(1, $ketua->notifications);
        $this->assertEquals('Penugasan Fast Review', $ketua->notifications->first()->data['title']);

        $this->assertCount(1, $sekertaris->notifications);
        $this->assertEquals('Penugasan Fast Review', $sekertaris->notifications->first()->data['title']);
    }
}
