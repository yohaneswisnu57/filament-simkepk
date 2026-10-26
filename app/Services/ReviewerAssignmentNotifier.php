<?php

namespace App\Services;

use App\Mail\ReviewAssignmentMail;
use App\Models\Protocol;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReviewerAssignmentNotifier
{
    /**
     * Antrekan email penugasan ke setiap reviewer yang punya email.
     *
     * @param  Collection<int, User>  $reviewers
     * @return int Jumlah email yang diantrekan.
     */
    public function sendAssignmentEmails(Protocol $protocol, Collection $reviewers): int
    {
        $sent = 0;

        foreach ($reviewers->unique('id') as $reviewer) {
            if (blank($reviewer->email)) {
                Log::warning('Reviewer tanpa email, email penugasan dilewati.', [
                    'user_id' => $reviewer->id,
                    'protocol_id' => $protocol->id,
                ]);

                continue;
            }

            Mail::to($reviewer->email)->queue(new ReviewAssignmentMail($protocol, $reviewer));
            $sent++;
        }

        return $sent;
    }
}
