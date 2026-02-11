<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Notifications\Notifiable;
use Kirschbaum\Commentions\Contracts\Commenter;
use Spatie\Permission\Traits\HasRoles;
// use Filament\Models\Contracts\Panel\FilamentUser;

class User extends Authenticatable implements \Filament\Models\Contracts\FilamentUser, Commenter
{
    use HasDatabaseNotifications, HasFactory, HasRoles, Notifiable, HasPanelShield;

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

    public function isKetuaDariKelompok(int $kelompokId)
    {
        // Cek apakah user ini terdaftar sebagai ketua di kelompok tersebut
        return ReviewerKelompok::where('id', 'like', $kelompokId)
            ->where('ketua_user_id', $this->id)
            ->exists();
    }

    // public function canAccessPanel(Panel $panel): bool
    // {

    //     if ($panel->getId() === 'admin') {
    //         return $this->hasRole('super_admin');
    //     }

    //     if ($panel->getId() === 'reviewer') {
    //         // User dengan role user biasa TIDAK akan bisa masuk sini
    //         return $this->hasRole(['reviewer', 'super_admin']);
    //     }

    //     // return true; // Panel 'user' terbuka untuk semua yang login
    //         // Pastikan reviewer juga return true
    //     return $this->hasRole(['admin', 'super_admin', 'reviewer', 'user', 'sekertaris']);

    //     // ATAU jika ingin meloloskan semua user yang punya verified email:
    //     // return $this->hasVerifiedEmail();
    // }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole(['admin', 'sekertaris']);
        }

        if ($panel->getId() === 'user') {
            return $this->hasRole(['user']);
        }

        if ($panel->getId() === 'reviewer') {
            // User dengan role user biasa TIDAK akan bisa masuk sini
            return $this->hasRole(['reviewer']);
        }

        // 4. PENTING: Ubah ini menjadi FALSE
        // Ini memastikan jika user tidak punya role yang cocok di atas, dia TIDAK BISA masuk.
        return false;
    }
}
