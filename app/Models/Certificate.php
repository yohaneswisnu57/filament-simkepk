<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'members' => 'array',
        'approval_date' => 'date',
    ];

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }
}
