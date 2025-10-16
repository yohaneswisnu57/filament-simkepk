<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewerKelompok extends Model
{
    //

    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'reviewer_kelompok_id');
    }
}
