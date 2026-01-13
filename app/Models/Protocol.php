<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\ProtocolObserver;


#[ObservedBy(ProtocolObserver::class)]
class Protocol extends Model
{

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

    public function document(){
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


}
