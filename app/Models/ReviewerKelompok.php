<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewerKelompok extends Model
{
    //

    use SoftDeletes;

    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'reviewer_kelompok_id');
    }


}
