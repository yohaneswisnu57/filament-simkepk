<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewerKelompok extends Model
{
    //

    use SoftDeletes;

    protected $guarded = [];

    // protected $fillable = ['nama_kelompok', 'is_active', 'ketua_user_id', 'created_by'];

    public function users()
    {
        return $this->hasMany(User::class, 'reviewer_kelompok_id');
    }

    public function assignedProtocols()
    {
        return $this->hasMany(Protocol::class, 'reviewer_kelompok_id');
    }

    public function anggota()
    {
        // Asumsi: Di tabel users ada kolom 'reviewer_kelompok_id'
        return $this->hasMany(User::class, 'reviewer_kelompok_id');
    }

    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_user_id');
    }
}
