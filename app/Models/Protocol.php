<?php

namespace App\Models;

use App\Observers\ProtocolObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\Commentions\Comment;
use Kirschbaum\Commentions\Contracts\Commentable;
use Kirschbaum\Commentions\HasComments;

#[ObservedBy(ProtocolObserver::class)]
class Protocol extends Model implements Commentable
{
    use HasComments;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

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

    public function reviewers()
    {
        return $this->belongsToMany(User::class, 'protocol_reviewers')
            ->withTimestamps()
            ->withPivot('role_in_review');
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
