---
name: add-field-to-model
description: Complete step-by-step workflow for adding a new field/column to an existing SIKEPK model, including migration, form, infolist, table, and test updates.
---

# Skill: Add Field to Existing Model (SIKEPK Convention)

Gunakan skill ini setiap kali diminta menambahkan field/kolom baru ke model yang sudah ada.

---

## Langkah-langkah

### Step 1 – Pahami Model & Tabel Target

Sebelum mulai, pastikan kamu tahu:

- Nama model (contoh: `Protocol`)
- Nama tabel di database (contoh: `protocols`)
- Tipe data field baru (string, integer, boolean, date, text, dll.)
- Apakah nullable atau required
- Apakah perlu soft delete
- Apakah perlu tampil di form / infolist / table

---

### Step 2 – Buat Migration

```bash
php artisan make:migration add_{field_name}_to_{table_name}_table --no-interaction
```

**Template migration (tambah kolom):**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('{table_name}', function (Blueprint $table): void {
            $table->string('{field_name}')->nullable()->after('{existing_column}');
            // atau:
            // $table->text('{field_name}')->nullable();
            // $table->boolean('{field_name}')->default(false);
            // $table->date('{field_name}')->nullable();
            // $table->foreignId('{field_name}')->nullable()->constrained('{related_table}');
        });
    }

    public function down(): void
    {
        Schema::table('{table_name}', function (Blueprint $table): void {
            $table->dropColumn('{field_name}');
        });
    }
};
```

> ⚠️ **PENTING:** Jika memodifikasi kolom yang sudah ada (bukan menambah baru), sertakan **SEMUA atribut lama** agar tidak hilang. Contoh: jika kolom lama adalah `$table->string('name')`, jangan hanya tulis `$table->string('name')->nullable()` – pastikan semua atribut sebelumnya tetap ada.

---

### Step 3 – Jalankan Migration

```bash
php artisan migrate --no-interaction
```

---

### Step 4 – Update Model

Cek model di `app/Models/{ModelName}.php`.

Jika model menggunakan `$guarded = []` (seperti Protocol), tidak perlu update `$fillable`.
Jika model menggunakan `$fillable`, tambahkan field baru:

```php
protected $fillable = [
    // field lama...
    '{field_name}',
];
```

Jika field perlu casting (date, boolean, enum, dll.):

```php
protected function casts(): array
{
    return [
        '{field_name}' => 'boolean',
        // atau: 'date', 'datetime', 'array', dll.
    ];
}
```

---

### Step 5 – Update Form Schema

Edit `app/Filament/Resources/{ModelName}s/Schemas/{ModelName}Form.php`.

Tambahkan komponen form yang sesuai ke dalam section yang relevan:

```php
// Tipe: Text
TextInput::make('{field_name}')
    ->label('{Label}')
    ->nullable(),

// Tipe: Select (dari options statis)
Select::make('{field_name}')
    ->label('{Label}')
    ->options([
        'value1' => 'Label 1',
        'value2' => 'Label 2',
    ])
    ->searchable(),

// Tipe: Select (dari relasi)
Select::make('{foreign_key}')
    ->label('{Label}')
    ->relationship('{relationName}', '{titleAttribute}')
    ->nullable(),

// Tipe: Date
DatePicker::make('{field_name}')
    ->label('{Label}')
    ->native(false)
    ->displayFormat('d/m/Y')
    ->closeOnDateSelection(true),

// Tipe: Boolean / Toggle
Toggle::make('{field_name}')
    ->label('{Label}')
    ->default(false),

// Tipe: File Upload
FileUpload::make('{field_name}')
    ->label('{Label}')
    ->disk('public')
    ->directory('{field_name}')
    ->acceptedFileTypes(['application/pdf'])
    ->maxSize(2048),
```

Tambahkan `->visible()` jika field hanya untuk role tertentu:

```php
->visible(fn () => auth()->user()->hasRole(['super_admin', 'admin']))
```

---

### Step 6 – Update Infolist Schema

Edit `app/Filament/Resources/{ModelName}s/Schemas/{ModelName}Infolist.php`.

```php
// Text biasa
TextEntry::make('{field_name}')
    ->label('{Label}')
    ->placeholder('-'),

// Date
TextEntry::make('{field_name}')
    ->label('{Label}')
    ->date('D d M Y')
    ->placeholder('-'),

// Badge (status)
TextEntry::make('{field_name}')
    ->label('{Label}')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'active'   => 'success',
        'inactive' => 'danger',
        default    => 'gray',
    }),

// Relasi
TextEntry::make('{relation}.{attribute}')
    ->label('{Label}')
    ->placeholder('-'),
```

---

### Step 7 – Update Table Column

Edit `app/Filament/Resources/{ModelName}s/Tables/{ModelName}sTable.php`.

```php
TextColumn::make('{field_name}')
    ->label('{Label}')
    ->searchable()     // tambahkan jika perlu search
    ->sortable()       // tambahkan jika perlu sort
    ->toggleable(isToggledHiddenByDefault: false),

// Untuk relasi:
TextColumn::make('{relation}.{attribute}')
    ->label('{Label}'),

// Untuk date:
TextColumn::make('{field_name}')
    ->label('{Label}')
    ->date('d M Y')
    ->sortable(),

// Untuk badge
TextColumn::make('{field_name}')
    ->label('{Label}')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'active' => 'success',
        default  => 'gray',
    }),
```

---

### Step 8 – Format Kode

```bash
vendor/bin/pint --dirty
```

---

### Step 9 – Update/Tulis Test

Jika ada test yang sudah ada untuk model ini, update test tersebut.
Jika belum ada, buat test baru:

```bash
php artisan test --filter={ModelName}
```

Pastikan:

- Factory sudah include field baru jika NOT NULL
- Test form menggunakan field baru dalam `fillForm()`

---

### Step 10 – Verifikasi

```bash
php artisan test --filter={ModelName}
```

---

## Contoh Nyata (dari SIKEPK)

Menambahkan field `contact_person` ke `protocols`:

1. Migration: `add_contact_person_to_protocols_table`
2. Form: `TextInput::make('contact_person')->tel()->label('Contact Person')->nullable()`
3. Infolist: `TextEntry::make('contact_person')->label('Contact Person')`
4. Table: `TextColumn::make('contact_person')->label('Contact Person')->searchable()`
