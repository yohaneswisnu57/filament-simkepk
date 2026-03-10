---
name: write-feature-test
description: Template and workflow for writing Filament feature tests in SIKEPK following the project's testing conventions (PHPUnit + RefreshDatabase + Spatie roles).
---

# Skill: Write Feature Test (SIKEPK Convention)

Gunakan skill ini setiap kali diminta menulis atau memperbaiki test di proyek SIKEPK.

---

## Aturan Dasar Testing di SIKEPK

1. **Setiap perubahan wajib ditest** — tulis test baru atau update yang sudah ada.
2. Gunakan `RefreshDatabase` di setiap test class.
3. Selalu setup roles di `setUp()` karena database di-refresh setiap run.
4. Selalu autentikasi user sebelum test resource Filament.
5. Gunakan `livewire()` helper, bukan `$this->get()`, untuk test komponen Filament.
6. Jalankan test minimal yang relevan: `php artisan test --filter=NamaTest`.

---

## Step 1 – Buat File Test

```bash
php artisan make:test {Feature}Test --no-interaction
```

Untuk unit test:

```bash
php artisan make:test {Feature}Test --unit --no-interaction
```

---

## Step 2 – Template Dasar Feature Test

```php
<?php

namespace Tests\Feature;

use App\Filament\Resources\{ModelName}s\Pages\Create{ModelName};
use App\Filament\Resources\{ModelName}s\Pages\Edit{ModelName};
use App\Filament\Resources\{ModelName}s\Pages\List{ModelName}s;
use App\Models\{ModelName};
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class {ModelName}ResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // WAJIB: Setup semua roles yang digunakan di project
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sekertaris',  'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'reviewer',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user',        'guard_name' => 'web']);

        // Buat user dengan peran berbeda untuk test
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('user');
    }

    // ==========================================
    // TEST: LIST (INDEX)
    // ==========================================

    /** @test */
    public function admin_can_list_{model_name}s(): void
    {
        $records = {ModelName}::factory()->count(3)->create();

        $this->actingAs($this->admin);

        livewire(List{ModelName}s::class)
            ->assertCanSeeTableRecords($records);
    }

    /** @test */
    public function user_cannot_see_other_users_{model_name}s(): void
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('user');

        $myRecord = {ModelName}::factory()->create(['user_id' => $this->regularUser->id]);
        $otherRecord = {ModelName}::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($this->regularUser);

        livewire(List{ModelName}s::class)
            ->assertCanSeeTableRecords([$myRecord])
            ->assertCanNotSeeTableRecords([$otherRecord]);
    }

    // ==========================================
    // TEST: CREATE
    // ==========================================

    /** @test */
    public function admin_can_create_{model_name}(): void
    {
        $this->actingAs($this->admin);

        livewire(Create{ModelName}::class)
            ->fillForm([
                'field_name' => 'Test Value',
                // tambahkan field lain yang required
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('{table_name}', [
            'field_name' => 'Test Value',
        ]);
    }

    /** @test */
    public function create_form_validates_required_fields(): void
    {
        $this->actingAs($this->admin);

        livewire(Create{ModelName}::class)
            ->fillForm([])
            ->call('create')
            ->assertHasFormErrors([
                'field_name' => 'required',
            ]);
    }

    // ==========================================
    // TEST: EDIT / UPDATE
    // ==========================================

    /** @test */
    public function admin_can_edit_{model_name}(): void
    {
        $record = {ModelName}::factory()->create();

        $this->actingAs($this->admin);

        livewire(Edit{ModelName}::class, ['record' => $record->getKey()])
            ->fillForm([
                'field_name' => 'Updated Value',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('{table_name}', [
            'id'         => $record->id,
            'field_name' => 'Updated Value',
        ]);
    }

    // ==========================================
    // TEST: DELETE
    // ==========================================

    /** @test */
    public function admin_can_delete_{model_name}(): void
    {
        $record = {ModelName}::factory()->create();

        $this->actingAs($this->admin);

        livewire(List{ModelName}s::class)
            ->callTableAction('delete', $record)
            ->assertHasNoActionErrors();

        $this->assertSoftDeleted('{table_name}', ['id' => $record->id]);
        // Jika tidak soft delete:
        // $this->assertDatabaseMissing('{table_name}', ['id' => $record->id]);
    }

    // ==========================================
    // TEST: SEARCH / FILTER
    // ==========================================

    /** @test */
    public function table_is_searchable(): void
    {
        $target = {ModelName}::factory()->create(['field_name' => 'Unique Keyword']);
        $other  = {ModelName}::factory()->create(['field_name' => 'Other Record']);

        $this->actingAs($this->admin);

        livewire(List{ModelName}s::class)
            ->searchTable('Unique Keyword')
            ->assertCanSeeTableRecords([$target])
            ->assertCanNotSeeTableRecords([$other]);
    }
}
```

---

## Step 3 – Template Test Notification / Observer

```php
/** @test */
public function creating_{model_name}_sends_notification_to_admins(): void
{
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($this->regularUser);

    {ModelName}::factory()->create(['user_id' => $this->regularUser->id]);

    // Assert database notification exists
    $this->assertDatabaseHas('notifications', [
        'notifiable_id'   => $admin->id,
        'notifiable_type' => User::class,
    ]);
}
```

---

## Step 4 – Template Test Authorization (Role-Based)

```php
/** @test */
public function reviewer_can_only_see_assigned_{model_name}s(): void
{
    $reviewer = User::factory()->create();
    $reviewer->assignRole('reviewer');

    $assigned   = {ModelName}::factory()->create(['reviewer_kelompok_id' => $reviewer->reviewer_kelompok_id]);
    $unassigned = {ModelName}::factory()->create();

    $this->actingAs($reviewer);

    livewire(List{ModelName}s::class)
        ->assertCanSeeTableRecords([$assigned])
        ->assertCanNotSeeTableRecords([$unassigned]);
}

/** @test */
public function user_without_permission_cannot_access_{model_name}_create(): void
{
    $unauthorized = User::factory()->create();
    $unauthorized->assignRole('user');

    $this->actingAs($unauthorized);

    // Filament redirects or shows 403 for unauthorized access
    $this->get(route('filament.admin.resources.{model_name}s.create'))
        ->assertForbidden();
}
```

---

## Step 5 – Template Test Pivot / Relasi Many-to-Many

Contoh dari SIKEPK: pivot `protocol_reviewers`:

```php
/** @test */
public function protocol_assigns_reviewer_via_pivot(): void
{
    $reviewer = User::factory()->create();
    $reviewer->assignRole('reviewer');

    $protocol = Protocol::factory()->create();

    $protocol->reviewers()->sync([
        $reviewer->id => ['role_in_review' => 'Ketua'],
    ]);

    $this->assertDatabaseHas('protocol_reviewers', [
        'protocol_id'    => $protocol->id,
        'user_id'        => $reviewer->id,
        'role_in_review' => 'Ketua',
    ]);
}
```

---

## Step 6 – Jalankan Test

```bash
# Jalankan satu file test
php artisan test tests/Feature/{Feature}Test.php

# Filter berdasarkan nama method
php artisan test --filter=admin_can_create_{model_name}

# Jalankan semua test
php artisan test
```

---

## Checklist Test

- [ ] Roles di-setup di `setUp()` dengan `firstOrCreate`
- [ ] User di-authenticate sebelum test resource
- [ ] Test positif (happy path) ada
- [ ] Test negatif (validasi, authorization) ada
- [ ] Factory digunakan untuk pembuatan data
- [ ] `assertDatabaseHas` / `assertDatabaseMissing` dipakai untuk verifikasi DB
- [ ] Tidak ada hardcoded ID — gunakan factory atau `firstOrCreate`

---

## Catatan Penting

- Gunakan `Filament::setCurrentPanel('admin')` jika menggunakan multiple panels.
- Untuk action di table: gunakan `->callTableAction('action_name', $record)`.
- Untuk action di header resource: gunakan `->callAction('action_name')`.
- Komponen Livewire Filament: `livewire(ListProtocols::class)` – bukan `Livewire::test(...)`.
