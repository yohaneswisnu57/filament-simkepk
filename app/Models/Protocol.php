<?php

namespace App\Models;

use App\Observers\ProtocolObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\Commentions\Comment;
use Kirschbaum\Commentions\Contracts\Commentable;
use Kirschbaum\Commentions\HasComments;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy(ProtocolObserver::class)]
class Protocol extends Model implements Commentable
{
    use HasComments;
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->useLogName('Protocol');
    }

    public function statusReview()
    {
        return $this->belongsTo(StatusReview::class, 'status_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function document()
    {
        return $this->hasMany(Document::class, 'protocol_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'protocol_id');
    }

    public function assignedReviewerKelompok()
    {
        return $this->belongsTo(ReviewerKelompok::class, 'reviewer_kelompok_id');
    }

    public function reviewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'protocol_reviewers')
            ->withTimestamps()
            ->withPivot(['role_in_review', 'feedback_status']);
    }

    /** Hanya reviews yang memiliki verdict (Fast Review) */
    public function fastReviews(): HasMany
    {
        return $this->hasMany(Review::class)->whereNotNull('verdict');
    }

    /** True jika semua reviewer yang di-assign sudah submit */
    public function allReviewersSubmitted(): bool
    {
        return $this->reviewers()
            ->wherePivot('feedback_status', 'pending')
            ->doesntExist();
    }

    /** True jika siap cetak certificate */
    public function isReadyForCertificate(): bool
    {
        return $this->fast_review_decision === 'Exempted' 
            || in_array($this->status_id, [1, 5]);
    }

    /** Peneliti hanya boleh update nama maksimal 2 kali (initial + 1 correction) */
    public function canResearcherUpdateName(): bool
    {
        return $this->certificate_name_changes < 2;
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function canComment(): bool
    {
        // Atur logika siapa yang bisa mengomentari
        return true; // Contoh: semua pengguna dapat mengomentari
    }
}
