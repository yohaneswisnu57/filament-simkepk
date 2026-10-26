<?php

namespace App\Console\Commands;

use App\Models\Protocol;
use App\Services\ReviewerAssignmentNotifier;
use Illuminate\Console\Command;

class ResendReviewerAssignmentEmails extends Command
{
    protected $signature = 'protocols:resend-reviewer-assignment
        {--user= : ID reviewer}
        {--since= : Hanya penugasan sejak tanggal ini (Y-m-d)}
        {--send : Kirim email sungguhan (default: dry-run)}';

    protected $description = 'Kirim ulang email penugasan untuk penugasan reviewer berstatus pending';

    public function handle(ReviewerAssignmentNotifier $notifier): int
    {
        $rows = \DB::table('protocol_reviewers')
            ->where('feedback_status', 'pending')
            ->when($this->option('user'), fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($this->option('since'), fn ($query, $since) => $query->where('created_at', '>=', $since))
            ->get();

        foreach ($rows as $row) {
            $protocol = Protocol::find($row->protocol_id);
            $reviewer = $protocol?->reviewers()->whereKey($row->user_id)->first();

            if (! $reviewer) {
                continue;
            }

            if ($this->option('send')) {
                $count = $notifier->sendAssignmentEmails($protocol, collect([$reviewer]));
                $this->line(($count ? 'DIKIRIM' : 'DILEWATI')." {$reviewer->email} protokol #{$protocol->id}");
            } else {
                $this->line("DRY-RUN {$reviewer->email} protokol #{$protocol->id} ({$row->role_in_review})");
            }
        }

        $this->info($rows->count().' penugasan pending ditemukan.'.($this->option('send') ? '' : ' Tambahkan --send untuk mengirim.'));

        return self::SUCCESS;
    }
}
