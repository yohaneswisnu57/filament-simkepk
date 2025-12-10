<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\ReviewerKelompok;


class User extends Authenticatable
{

    use HasFactory, Notifiable, HasRoles, HasDatabaseNotifications;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function protocols()
    {
        return $this->hasMany(Protocol::class, 'user_id');
    }

    public function reviewerKelompok()
    {
        return $this->belongsTo(ReviewerKelompok::class, 'reviewer_kelompok_id', 'id');

    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }


    public function isKetuaDariKelompok(int $kelompokId): bool
    {
        // Cek apakah user ini terdaftar sebagai ketua di kelompok tersebut
        return ReviewerKelompok::where('id', $kelompokId)
            ->where('ketua_user_id', $this->id)
            ->exists();
    }

}
