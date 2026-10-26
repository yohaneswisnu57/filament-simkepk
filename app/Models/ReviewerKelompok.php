<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class ReviewerKelompok extends Model
{
    //

    use SoftDeletes;

    protected $fillable = ['nama_kelompok', 'is_active', 'ketua_user_id', 'created_by'];

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

    /**
     * Anggota kelompok ditambah ketua, unik dan hanya yang aktif.
     *
     * @return Collection<int, User>
     */
    public function recipients(): Collection
    {
        return User::query()
            ->where(function ($query) {
                $query->where('reviewer_kelompok_id', $this->id);

                if ($this->ketua_user_id) {
                    $query->orWhere('id', $this->ketua_user_id);
                }
            })
            ->where('is_active', true)
            ->get()
            ->unique('id')
            ->values();
    }

    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_user_id');
    }
}
