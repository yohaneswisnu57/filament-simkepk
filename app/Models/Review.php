<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'protocol_id',
        'user_id',
        'comment',
    ];

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
