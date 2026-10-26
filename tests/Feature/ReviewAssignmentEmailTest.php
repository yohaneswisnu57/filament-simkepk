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

    private function makeGroup(string $name, bool $active = true): array
    {
        $ketua = User::factory()->create();
        $member = User::factory()->create();
        $group = ReviewerKelompok::create([
            'nama_kelompok' => $name,
            'is_active' => $active,
            'ketua_user_id' => $ketua->id,
        ]);
        $member->update(['reviewer_kelompok_id' => $group->id]);

        return [$group, $ketua, $member];
    }

    #[Test]
    public function changing_group_emails_new_group_and_notifies_old_group_without_email(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        [$groupA, , $memberA] = $this->makeGroup('A');
        [$groupB, , $memberB] = $this->makeGroup('B');
        $protocol = Protocol::factory()->create(['reviewer_kelompok_id' => $groupA->id]);

        Mail::fake();
        $protocol->update(['reviewer_kelompok_id' => $groupB->id]);

        Mail::assertQueued(ReviewAssignmentMail::class, fn ($mail) => $mail->hasTo($memberB->email));
        Mail::assertNotQueued(ReviewAssignmentMail::class, fn ($mail) => $mail->hasTo($memberA->email));
        $this->assertSame(1, $memberA->notifications()->where('data->title', 'Group Assignment Removed')->count());
    }

    #[Test]
    public function leaving_fast_review_to_expedited_with_existing_group_emails_group_once(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        [$group, $ketua, $member] = $this->makeGroup('A');
        $protocol = Protocol::factory()->create(['status_id' => 6, 'reviewer_kelompok_id' => $group->id]);

        Mail::fake();
        $protocol->update(['status_id' => 3]);

        Mail::assertQueued(ReviewAssignmentMail::class, 2);
        Mail::assertQueued(ReviewAssignmentMail::class, fn ($mail) => $mail->hasTo($member->email));
        Mail::assertQueued(ReviewAssignmentMail::class, fn ($mail) => $mail->hasTo($ketua->email));
    }

    #[Test]
    public function inactive_users_are_excluded_from_group_recipients(): void
    {
        [$group, , $member] = $this->makeGroup('A');
        $member->update(['is_active' => false]);

        $this->assertFalse($group->recipients()->contains('id', $member->id));
        $this->assertCount(1, $group->recipients());
    }
}
