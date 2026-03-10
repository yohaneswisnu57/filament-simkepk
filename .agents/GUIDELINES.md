# SIKEPK – AI Coding Guidelines

> Panduan ini dibuat khusus untuk membantu AI agent bekerja dengan tepat, konsisten, dan efisien dalam proyek **SIKEPK (Sistem Informasi Kelayakan Protokol Etik)**.

---

## 1. STACK & VERSI

| Teknologi         | Versi          | Catatan                                      |
| ----------------- | -------------- | -------------------------------------------- |
| PHP               | 8.4.1          | Gunakan fitur modern (enum, named args, dll) |
| Laravel           | 12.x           | Streamlined structure (no Kernel.php)        |
| Filament          | 4.x            | SDUI – semua UI didefinisikan dari PHP       |
| Livewire          | 3.x            | `wire:model` deferred by default             |
| Tailwind CSS      | 4.x            | `@import "tailwindcss"` bukan `@tailwind`    |
| Alpine.js         | 3.x            | Sudah bundled dalam Livewire                 |
| Spatie Permission | latest         | RBAC dengan policies                         |
| Filament Shield   | 4.x            | Auto-generate permissions                    |
| Vite              | 7.x            | Build tool frontend                          |
| Database          | SQLite / MySQL | Default SQLite untuk development             |

---

## 2. STRUKTUR DIREKTORI

```
app/
├── Filament/
│   ├── Pages/              ← Custom panel pages
│   ├── Resources/
│   │   ├── Protocols/
│   │   │   ├── Pages/         ← List, Create, Edit, View
│   │   │   ├── RelationManagers/
│   │   │   ├── Schemas/       ← ProtocolForm.php, ProtocolInfolist.php
│   │   │   ├── Tables/        ← ProtocolsTable.php
│   │   │   └── Widgets/
│   │   ├── Users/
│   │   ├── ReviewerKelompoks/
│   │   ├── StatusReviews/
│   │   └── Documents/
│   └── Widgets/
├── Models/                 ← Eloquent models
├── Observers/              ← ProtocolObserver, dll.
├── Policies/               ← Policy per model
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php
```

> **Aturan:** Jangan membuat folder baru di luar struktur ini tanpa diskusi. Selalu cek sibling files untuk konvensi yang sudah ada.

---

## 3. FILAMENT PATTERNS (SANGAT PENTING)

### 3.1 Resource Pattern

Setiap Resource **wajib** memisahkan Form, Infolist, dan Table ke file terpisah di subfolder `Schemas/` dan `Tables/`:

```php
// ProtocolResource.php
public static function form(Schema $schema): Schema
{
    return ProtocolForm::configure($schema);
}

public static function infolist(Schema $schema): Schema
{
    return ProtocolInfolist::configure($schema);
}

public static function table(Table $table): Table
{
    return ProtocolsTable::configure($table);
}
```

### 3.2 Schema Class Pattern

```php
class ProtocolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Section Title')
                ->columns(2)
                ->schema([
                    // komponen di sini
                ]),
        ]);
    }
}
```

### 3.3 Filament v4 – Perubahan Penting

- **Import Schemas:** Gunakan `Filament\Schemas\Components\Section`, bukan `Filament\Forms\Components`.
- **Actions:** Semua action extend `Filament\Actions\Action` – tidak ada `Filament\Tables\Actions`.
- **Icons:** Gunakan `Filament\Support\Icons\Heroicon` enum.
- **Visibility default private:** File visibility sekarang `private` by default.
- **DeferFilters:** Sekarang default behavior, gunakan `deferFilters(false)` untuk disable.

### 3.4 Authorization di Form/Table

Gunakan `->visible()` untuk menyembunyikan field dari peran yang tidak berhak:

```php
->visible(fn () => auth()->user()->hasRole(['super_admin', 'admin']))
```

### 3.5 Dehydrated Fields

Untuk field virtual (tidak ada kolomnya di database), selalu tambahkan `->dehydrated(false)`:

```php
Select::make('fast_review_ketua_id')
    ->dehydrated(false) // tidak ada kolom ini di tabel protocols
```

---

## 4. AUTHORIZATION & ROLES

### Roles yang ada:

| Role          | Hak Akses                                    |
| ------------- | -------------------------------------------- |
| `super_admin` | Full access semua resource                   |
| `admin`       | Full access + settings                       |
| `sekertaris`  | View/manage protocols dan assignment         |
| `reviewer`    | Lihat protokol yang di-assign, submit review |
| `user`        | Submit dan lihat protokol milik sendiri      |

### Policy Pattern:

```php
public function viewAny(User $user): bool
{
    return $user->can('ViewAny:Protocol');
}
```

Format permission: `Action:Model` (PascalCase). Contoh: `ViewAny:Protocol`, `Create:User`.

### Row-Level Filtering (`getEloquentQuery()`):

```php
// Admin/sekertaris melihat semua
if ($user->hasRole(['super_admin', 'admin', 'sekertaris'])) {
    return $query;
}

// Reviewer hanya melihat yang relevan
$query->where(function (Builder $q) use ($user) {
    $q->where('user_id', $user->id);
    if ($user->hasRole('reviewer') && $user->reviewer_kelompok_id) {
        $q->orWhereHas('reviewers', fn ($q2) => $q2->where('users.id', $user->id));
        $q->orWhere('reviewer_kelompok_id', $user->reviewer_kelompok_id);
    }
});
```

---

## 5. MODELS & DATABASE

### Model Conventions:

- **Soft Deletes:** Wajib untuk `Protocol`, `Document`, `ReviewerKelompok`, `StatusReview`.
- **Observer:** Attach via attribute `#[ObservedBy(ProtocolObserver::class)]`.
- **Casts:** Gunakan method `casts()` bukan property `$casts`.
- **Relationships:** Selalu sertakan return type hint.

```php
use App\Observers\ProtocolObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(ProtocolObserver::class)]
class Protocol extends Model
{
    use SoftDeletes;

    public function statusReview(): BelongsTo
    {
        return $this->belongsTo(StatusReview::class, 'status_id');
    }
}
```

### Pivot Table `protocol_reviewers`:

Protocol memiliki `reviewers()` via `belongsToMany` dengan pivot `role_in_review` (nilai: `'Ketua'`, `'Sekertaris'`):

```php
public function reviewers(): BelongsToMany
{
    return $this->belongsToMany(User::class, 'protocol_reviewers')
        ->withTimestamps()
        ->withPivot('role_in_review');
}
```

### Migration Rules:

- Saat **modify column**, sertakan semua atribut lama agar tidak hilang.
- Disable foreign key constraints saat drop/create tabel dengan relasi.
- Gunakan soft deletes untuk data sensitif.

---

## 6. PHP CODING STANDARDS

```php
// ✅ Constructor property promotion
public function __construct(public ProtocolRepository $repository) {}

// ✅ Explicit return types
public function storeProtocol(Protocol $protocol): bool
{
    return $this->repository->save($protocol);
}

// ✅ Curly braces selalu ada
if ($condition) {
    doSomething();
}

// ✅ PHPDoc untuk tipe kompleks
/** @param array<string, mixed> $data */
public function processData(array $data): void {}
```

- **Enums:** TitleCase key (misal: `ReviewStatus::UnderRevision`)
- **Variables & Methods:** camelCase
- **Constants:** UPPER_SNAKE_CASE
- **Jangan** gunakan `env()` langsung di luar config — gunakan `config('key')`

---

## 7. LARAVEL CONVENTIONS

- Gunakan `Model::query()`, hindari `DB::` raw
- Eager load untuk cegah N+1: `Protocol::with(['statusReview', 'user'])->get()`
- Gunakan named routes: `route('filament.admin.resources.protocols.index')`
- Form validation → wajib dengan **Form Request class**, bukan inline di controller
- Queue untuk operasi lama → `ShouldQueue`

---

## 8. FILE UPLOAD

```php
FileUpload::make('uploadpernyataan')
    ->disk('public')
    ->directory('uploadpernyataan')
    ->preserveFilenames()
    ->acceptedFileTypes(['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
    ->maxSize(2048) // 2MB
```

Storage: `storage/app/public/` → Access via `asset('storage/uploadpernyataan/file.pdf')`

---

## 9. TESTING

- Setiap perubahan **wajib** ditest.
- Gunakan factory untuk buat model dalam test.
- Authenticate dulu sebelum test resource:

```php
$user = User::factory()->create();
$user->assignRole('admin');
$this->actingAs($user);

livewire(ListProtocols::class)
    ->assertCanSeeTableRecords($protocols);
```

- Jalankan test minimal yang relevan: `php artisan test --filter=ProtocolTest`
- Gunakan `php artisan make:test ProtocolTest` untuk feature test.

---

## 10. WORKFLOW UMUM

### Tambah Field Baru ke Protocol:

1. `php artisan make:migration add_field_to_protocols_table --no-interaction`
2. Update `Protocol` model (`$guarded = []` atau tambah ke `$fillable`)
3. Update `ProtocolForm.php` (form)
4. Update `ProtocolInfolist.php` (view)
5. Update `ProtocolsTable.php` (table column)
6. `php artisan migrate`
7. `vendor/bin/pint --dirty`
8. `php artisan test --filter=Protocol`

### Buat Resource Baru:

```bash
php artisan make:filament-resource NewModel --generate --no-interaction
php artisan filament:shield-generate --no-interaction
```

### Tambah Permission/Role:

```bash
php artisan tinker
# Role::create(['name' => 'new_role', 'guard_name' => 'web']);
```

---

## 11. PERINTAH PENTING

```bash
# Development
composer run dev              # Start semua: server + queue + pail + vite

# Formatting (WAJIB sebelum selesai)
vendor/bin/pint --dirty

# Testing
php artisan test
php artisan test --filter=NamaTest

# Cache
php artisan config:clear
php artisan filament:optimize-clear

# Shield
php artisan filament:shield-generate

# Tinker
php artisan tinker
```

---

## 12. CHECKLIST SEBELUM SELESAI

- [ ] Kode mengikuti konvensi file sibling yang ada
- [ ] Return type eksplisit pada semua method
- [ ] Tidak ada `env()` di luar config
- [ ] Tidak ada N+1 query (eager load sudah betul)
- [ ] Authorization/policy sudah dicek (`->visible()`, `getEloquentQuery()`)
- [ ] Soft delete dipakai untuk data sensitif
- [ ] `vendor/bin/pint --dirty` sudah dijalankan
- [ ] Test sudah ditulis dan passed
- [ ] Tidak ada kolom yang hilang saat modifikasi migration

---

> **Catatan:** Selalu baca file sibling sebelum menulis kode baru. Jika ragu, tanyakan dulu jangan asumsikan.
