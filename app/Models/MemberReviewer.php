<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberReviewer extends Model
{
    //
    protected $fillable = [
        'reviewer_kelompok_id',
        'user_id',
    ];

    protected $table = 'member_reviewers';
}
