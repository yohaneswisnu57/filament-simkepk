<?php

namespace Tests\Feature;

use App\Mail\ReviewAssignmentMail;
use App\Models\Protocol;
use App\Models\ReviewerKelompok;
use App\Models\User;
use App\Services\ReviewerAssignmentNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReviewAssignmentEmailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_emails_group_members_and_group_ketua_when_group_is_assigned(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $ketua = User::factory()->create();
        $member = User::factory()->create();
        $outsider = User::factory()->create();

        $group = ReviewerKelompok::create([
            'nama_kelompok' => 'Kelompok A',
            'is_active' => true,
            'ketua_user_id' => $ketua->id,
        ]);
        $member->update(['reviewer_kelompok_id' => $group->id]);

        $protocol = Protocol::factory()->create();

        Mail::fake();
        $protocol->update(['reviewer_kelompok_id' => $group->id]);

        Mail::assertQueued(ReviewAssignmentMail::class, fn ($mail) => $mail->hasTo($member->email));
        Mail::assertQueued(ReviewAssignmentMail::class, fn ($mail) => $mail->hasTo($ketua->email));
        Mail::assertNotQueued(ReviewAssignmentMail::class, fn ($mail) => $mail->hasTo($outsider->email));
    }

    #[Test]
    public function notifier_skips_reviewers_without_email_and_dedupes(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $protocol = Protocol::factory()->create();
        $withEmail = User::factory()->create();
        $withoutEmail = User::factory()->make(['email' => '']);
        $withoutEmail->id = 99999;

        Mail::fake();
        $sent = app(ReviewerAssignmentNotifier::class)
            ->sendAssignmentEmails($protocol, collect([$withEmail, $withEmail, $withoutEmail]));

        $this->assertSame(1, $sent);
        Mail::assertQueuedCount(1);
    }

    #[Test]
    public function resend_command_is_dry_run_by_default_and_sends_with_flag(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $reviewer = User::factory()->create();
        $protocol = Protocol::factory()->create();
        $protocol->reviewers()->attach($reviewer->id, ['role_in_review' => 'Ketua', 'feedback_status' => 'pending']);

        Mail::fake();
        $this->artisan('protocols:resend-reviewer-assignment', ['--user' => $reviewer->id])->assertSuccessful();
        Mail::assertNothingQueued();

        $this->artisan('protocols:resend-reviewer-assignment', ['--user' => $reviewer->id, '--send' => true])->assertSuccessful();
        Mail::assertQueued(ReviewAssignmentMail::class, fn ($mail) => $mail->hasTo($reviewer->email));
    }
}
