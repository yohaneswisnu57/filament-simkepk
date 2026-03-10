<?php

namespace Tests\Feature;

use App\Models\Protocol;
use App\Models\Review;
use App\Models\StatusReview;
use App\Models\User;
use App\Services\FastReviewDecisionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FastReviewAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $ketua;

    protected User $sekertaris;

    protected Protocol $protocol;

    protected StatusReview $statusFastReview;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Roles
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sekertaris', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'reviewer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->ketua = User::factory()->create();
        $this->ketua->assignRole('reviewer');

        $this->sekertaris = User::factory()->create();
        $this->sekertaris->assignRole('sekertaris');

        $this->statusFastReview = StatusReview::create(['status_name' => 'Fast Review']);

        $this->protocol = Protocol::factory()->create([
            'status_id' => $this->statusFastReview->id,
        ]);
    }

    // ──────────────────────────────────────────────────
    // TEST: Manual assignment ke pivot dengan feedback_status=pending
    // ──────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_manually_assigns_ketua_and_sekertaris_to_protocol_reviewers(): void
    {
        // Simulasi admin melakukan manual assign (seperti yang dilakukan di afterSave EditProtocol)
        $this->protocol->reviewers()->attach($this->ketua->id, [
            'role_in_review' => 'Ketua',
            'feedback_status' => 'pending',
        ]);

        $this->protocol->reviewers()->attach($this->sekertaris->id, [
            'role_in_review' => 'Sekertaris',
            'feedback_status' => 'pending',
        ]);

        $this->protocol->update(['fast_review_decision' => 'Pending']);

        $this->assertDatabaseHas('protocol_reviewers', [
            'protocol_id' => $this->protocol->id,
            'user_id' => $this->ketua->id,
            'role_in_review' => 'Ketua',
            'feedback_status' => 'pending',
        ]);

        $this->assertDatabaseHas('protocol_reviewers', [
            'protocol_id' => $this->protocol->id,
            'user_id' => $this->sekertaris->id,
            'role_in_review' => 'Sekertaris',
            'feedback_status' => 'pending',
        ]);

        $this->assertDatabaseHas('protocols', [
            'id' => $this->protocol->id,
            'fast_review_decision' => 'Pending',
        ]);
    }

    // ──────────────────────────────────────────────────
    // TEST: Decision engine → semua Exempted
    // ──────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function decision_engine_marks_exempted_when_all_verdicts_are_exempted(): void
    {
        // Assign 2 reviewer
        $this->protocol->reviewers()->attach($this->ketua->id, [
            'role_in_review' => 'Ketua',
            'feedback_status' => 'pending',
        ]);
        $this->protocol->reviewers()->attach($this->sekertaris->id, [
            'role_in_review' => 'Sekertaris',
            'feedback_status' => 'pending',
        ]);
        $this->protocol->update(['fast_review_decision' => 'Pending']);

        // Reviewer 1 submit Exempted
        Review::create([
            'protocol_id' => $this->protocol->id,
            'user_id' => $this->ketua->id,
            'comment' => 'Protokol sudah sesuai standar.',
            'verdict' => 'Exempted',
            'submitted_at' => now(),
        ]);
        $this->protocol->reviewers()->updateExistingPivot($this->ketua->id, [
            'feedback_status' => 'submitted',
        ]);

        // Decision engine → masih pending (reviewer 2 belum submit)
        app(FastReviewDecisionService::class)->evaluate($this->protocol->fresh());
        $this->assertDatabaseHas('protocols', [
            'id' => $this->protocol->id,
            'fast_review_decision' => 'Pending', // belum berubah
        ]);

        // Reviewer 2 submit Exempted
        Review::create([
            'protocol_id' => $this->protocol->id,
            'user_id' => $this->sekertaris->id,
            'comment' => 'Setuju, protokol aman.',
            'verdict' => 'Exempted',
            'submitted_at' => now(),
        ]);
        $this->protocol->reviewers()->updateExistingPivot($this->sekertaris->id, [
            'feedback_status' => 'submitted',
        ]);

        // Decision engine → semua Exempted → fast_review_decision = 'Exempted'
        app(FastReviewDecisionService::class)->evaluate($this->protocol->fresh());

        $this->assertDatabaseHas('protocols', [
            'id' => $this->protocol->id,
            'fast_review_decision' => 'Exempted',
        ]);
    }

    // ──────────────────────────────────────────────────
    // TEST: Decision engine → ada 1 Full Board → eskalasi
    // ──────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function decision_engine_escalates_to_full_board_when_any_verdict_is_full_board(): void
    {
        StatusReview::create(['status_name' => 'Full Board']);

        $this->protocol->reviewers()->attach($this->ketua->id, [
            'role_in_review' => 'Ketua',
            'feedback_status' => 'pending',
        ]);
        $this->protocol->reviewers()->attach($this->sekertaris->id, [
            'role_in_review' => 'Sekertaris',
            'feedback_status' => 'pending',
        ]);

        // Reviewer 1 submit Exempted
        Review::create([
            'protocol_id' => $this->protocol->id,
            'user_id' => $this->ketua->id,
            'comment' => 'Saya setuju Exempted.',
            'verdict' => 'Exempted',
            'submitted_at' => now(),
        ]);
        $this->protocol->reviewers()->updateExistingPivot($this->ketua->id, [
            'feedback_status' => 'submitted',
        ]);

        // Reviewer 2 submit Full Board
        Review::create([
            'protocol_id' => $this->protocol->id,
            'user_id' => $this->sekertaris->id,
            'comment' => 'Perlu telaah lebih lanjut.',
            'verdict' => 'Full Board',
            'submitted_at' => now(),
        ]);
        $this->protocol->reviewers()->updateExistingPivot($this->sekertaris->id, [
            'feedback_status' => 'submitted',
        ]);

        // Decision engine → ada Full Board → eskalasi
        app(FastReviewDecisionService::class)->evaluate($this->protocol->fresh());

        $this->assertDatabaseHas('protocols', [
            'id' => $this->protocol->id,
            'fast_review_decision' => 'Full Board',
        ]);
    }

    // ──────────────────────────────────────────────────
    // TEST: allReviewersSubmitted() helper
    // ──────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function all_reviewers_submitted_returns_false_when_any_is_pending(): void
    {
        $this->protocol->reviewers()->attach($this->ketua->id, [
            'role_in_review' => 'Ketua',
            'feedback_status' => 'pending',
        ]);
        $this->protocol->reviewers()->attach($this->sekertaris->id, [
            'role_in_review' => 'Sekertaris',
            'feedback_status' => 'submitted',
        ]);

        $this->assertFalse($this->protocol->allReviewersSubmitted());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function all_reviewers_submitted_returns_true_when_all_submitted(): void
    {
        $this->protocol->reviewers()->attach($this->ketua->id, [
            'role_in_review' => 'Ketua',
            'feedback_status' => 'submitted',
        ]);
        $this->protocol->reviewers()->attach($this->sekertaris->id, [
            'role_in_review' => 'Sekertaris',
            'feedback_status' => 'submitted',
        ]);

        $this->assertTrue($this->protocol->allReviewersSubmitted());
    }
}
