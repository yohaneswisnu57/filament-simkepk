---
name: manage-roles-permissions
description: Guide for managing Spatie roles, permissions, and Filament Shield in SIKEPK — including creating roles, assigning permissions, and regenerating Shield capabilities.
---

# Skill: Manage Roles & Permissions (SIKEPK Convention)

Gunakan skill ini setiap kali diminta membuat role baru, assign permission, atau update pengaturan akses di SIKEPK.

---

## Konteks SIKEPK

SIKEPK menggunakan **Spatie Laravel Permission** + **Filament Shield** untuk RBAC.

### Roles yang ada:

| Role          | Deskripsi                                                   |
| ------------- | ----------------------------------------------------------- |
| `super_admin` | Full access ke semua resource dan settings                  |
| `admin`       | Full access ke semua resource                               |
| `sekertaris`  | Kelola protokol, assignment reviewer, lihat semua data      |
| `reviewer`    | Lihat protokol yang di-assign ke kelompoknya, submit review |
| `user`        | Submit protokol baru, lihat protokol milik sendiri          |

### Format Permission Filament Shield:

`{Action}:{ModelName}` — PascalCase keduanya.

Contoh: `ViewAny:Protocol`, `Create:Protocol`, `Update:User`, `Delete:ReviewerKelompok`

---

## Cara 1 – Menggunakan Tinker (Cepat, Development)

```bash
php artisan tinker
```

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Buat role baru
$role = Role::firstOrCreate(['name' => 'nama_role', 'guard_name' => 'web']);

// Buat permission baru
$perm = Permission::firstOrCreate(['name' => 'ViewAny:Model', 'guard_name' => 'web']);

// Assign permission ke role
$role->givePermissionTo($perm);

// Assign permission ke user
$user = \App\Models\User::find(1);
$user->assignRole('nama_role');
$user->givePermissionTo('ViewAny:Model');

// Cek permissions user
$user->getAllPermissions()->pluck('name');
$user->getRoleNames();
```

---

## Cara 2 – Menggunakan Seeder (Permanen, Recommended)

Edit atau buat seeder di `database/seeders/`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==========================================
        // 1. BUAT PERMISSIONS
        // ==========================================
        $permissions = [
            // Protocol
            'ViewAny:Protocol', 'View:Protocol', 'Create:Protocol',
            'Update:Protocol', 'Delete:Protocol', 'Restore:Protocol',
            'ForceDelete:Protocol', 'ForceDeleteAny:Protocol', 'RestoreAny:Protocol',

            // User
            'ViewAny:User', 'View:User', 'Create:User',
            'Update:User', 'Delete:User',

            // Tambahkan permission lain sesuai kebutuhan
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ==========================================
        // 2. BUAT ROLES
        // ==========================================
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        $sekertaris = Role::firstOrCreate(['name' => 'sekertaris',  'guard_name' => 'web']);
        $reviewer   = Role::firstOrCreate(['name' => 'reviewer',    'guard_name' => 'web']);
        $user       = Role::firstOrCreate(['name' => 'user',        'guard_name' => 'web']);

        // ==========================================
        // 3. ASSIGN PERMISSIONS KE ROLES
        // ==========================================

        // Super admin: semua permission
        $superAdmin->syncPermissions(Permission::all());

        // Admin: semua permission
        $admin->syncPermissions(Permission::all());

        // Sekertaris: lihat dan manage protokol, tapi tidak bisa hapus user
        $sekertaris->syncPermissions([
            'ViewAny:Protocol', 'View:Protocol', 'Update:Protocol',
            'ViewAny:User', 'View:User',
        ]);

        // Reviewer: hanya lihat protocol
        $reviewer->syncPermissions([
            'ViewAny:Protocol', 'View:Protocol',
        ]);

        // User: submit dan lihat protokol milik sendiri
        $user->syncPermissions([
            'ViewAny:Protocol', 'View:Protocol', 'Create:Protocol',
        ]);
    }
}
```

Jalankan seeder:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder --no-interaction
```

---

## Cara 3 – Auto-Generate via Filament Shield

Filament Shield dapat auto-generate permissions untuk semua Resource yang ada:

```bash
php artisan filament:shield-generate --no-interaction
```

Perintah ini membuat permissions berdasarkan semua Resource yang terdaftar di panel.

Untuk satu resource saja:

```bash
php artisan filament:shield-generate --resource=Protocol --no-interaction
```

---

## Menambahkan Role Baru

1. **Buat role via Tinker:**

```php
use Spatie\Permission\Models\Role;
Role::firstOrCreate(['name' => 'nama_role_baru', 'guard_name' => 'web']);
```

2. **Update `setUp()` di semua test** yang menggunakan `RefreshDatabase`:

```php
Role::firstOrCreate(['name' => 'nama_role_baru', 'guard_name' => 'web']);
```

3. **Update Policy** jika role baru perlu akses resource tertentu:

```php
public function viewAny(AuthUser $authUser): bool
{
    return $authUser->hasRole(['admin', 'super_admin', 'nama_role_baru']);
    // atau:
    return $authUser->can('ViewAny:Protocol');
}
```

4. **Update `getEloquentQuery()`** di Resource jika perlu row-level filtering untuk role baru.

5. **Update form `->visible()`** jika ada field yang harus tampil/sembunyi untuk role baru.

---

## Row-Level Authorization Pattern

Gunakan `getEloquentQuery()` di Resource untuk filter data per role:

```php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    // Role dengan full access — kembalikan semua data
    if ($user->hasRole(['super_admin', 'admin', 'sekertaris'])) {
        return $query;
    }

    // Reviewer — lihat yang di-assign ke mereka
    if ($user->hasRole('reviewer')) {
        return $query->where(function (Builder $q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('reviewers', fn ($q2) => $q2->where('users.id', $user->id))
              ->orWhere('reviewer_kelompok_id', $user->reviewer_kelompok_id);
        });
    }

    // User biasa — hanya milik sendiri
    return $query->where('user_id', $user->id);
}
```

---

## Field Visibility Pattern

Sembunyikan field dari role yang tidak berhak:

```php
// Satu role
->visible(fn () => auth()->user()->hasRole('super_admin'))

// Beberapa role
->visible(fn () => auth()->user()->hasRole(['super_admin', 'admin']))

// Kondisi kompleks
->visible(function ($get) {
    if (!auth()->user()->hasRole(['admin', 'sekertaris'])) {
        return false;
    }
    // logika tambahan berdasarkan state form lain
    return $get('status_id') !== null;
})
```

---

## Clear Permission Cache

Setelah update role/permission, clear cache:

```bash
php artisan cache:clear
php artisan config:clear
```

Atau dari PHP:

```php
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```

---

## Debugging Authorization

Cek permissions user via Tinker:

```php
$user = \App\Models\User::find(1);

// Cek roles
$user->getRoleNames();           // ['admin', ...]
$user->hasRole('admin');         // true/false

// Cek permissions
$user->getAllPermissions()->pluck('name');
$user->can('ViewAny:Protocol');  // true/false

// Cek via Policy
$user->can('viewAny', \App\Models\Protocol::class);
```

---

## Checklist

- [ ] Role baru ditambahkan ke `setUp()` di semua test yang pakai `RefreshDatabase`
- [ ] Policy di-update jika role baru perlu akses
- [ ] `getEloquentQuery()` di-update jika perlu row-level filter
- [ ] Form `->visible()` di-update jika ada field yang role-specific
- [ ] Permission cache di-clear setelah perubahan
- [ ] Test authorization ditulis untuk memastikan role baru bekerja benar
