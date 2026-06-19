<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'protocol_id',
        'certificate_number',
        'institution_name',
        'members',
        'approval_date',
        'file_path',
    ];

    protected $casts = [
        'members' => 'array',
        'approval_date' => 'date',
    ];

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }
}
