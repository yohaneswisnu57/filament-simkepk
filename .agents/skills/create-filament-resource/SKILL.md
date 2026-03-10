---
name: create-filament-resource
description: Step-by-step guide for creating a new Filament Resource following SIKEPK project conventions (Laravel 12 + Filament v4 + Spatie Permission)
---

# Skill: Create Filament Resource (SIKEPK Convention)

Gunakan skill ini setiap kali diminta membuat resource Filament baru dalam proyek SIKEPK.
Ikuti urutan langkah ini **tanpa melewati satu pun**.

---

## Step 1 – Generate Resource via Artisan

```bash
php artisan make:filament-resource {ModelName} --generate --no-interaction
```

Ini akan membuat:

- `app/Filament/Resources/{ModelName}Resource.php`
- `app/Filament/Resources/{ModelName}s/Pages/List{ModelName}s.php`
- `app/Filament/Resources/{ModelName}s/Pages/Create{ModelName}.php`
- `app/Filament/Resources/{ModelName}s/Pages/Edit{ModelName}.php`

---

## Step 2 – Refactor ke Struktur SIKEPK

Resource di SIKEPK **memisahkan** Form, Infolist, dan Table ke file terpisah.
Setelah generate, refactor struktur menjadi:

```
app/Filament/Resources/{ModelName}s/
├── {ModelName}Resource.php
├── Pages/
│   ├── List{ModelName}s.php
│   ├── Create{ModelName}.php
│   ├── Edit{ModelName}.php
│   └── View{ModelName}.php         ← Buat manual jika belum ada
├── Schemas/
│   ├── {ModelName}Form.php         ← Pindah form dari Resource
│   └── {ModelName}Infolist.php     ← Buat infolist
└── Tables/
    └── {ModelName}sTable.php       ← Pindah table dari Resource
```

### Template `{ModelName}Resource.php` (sesuai konvensi):

```php
<?php

namespace App\Filament\Resources\{ModelName}s;

use App\Filament\Resources\{ModelName}s\Schemas\{ModelName}Form;
use App\Filament\Resources\{ModelName}s\Schemas\{ModelName}Infolist;
use App\Filament\Resources\{ModelName}s\Tables\{ModelName}sTable;
use App\Filament\Resources\{ModelName}s\Pages\{...};
use App\Models\{ModelName};
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class {ModelName}Resource extends Resource
{
    protected static ?string $model = {ModelName}::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::SomeIcon;

    public static function form(Schema $schema): Schema
    {
        return {ModelName}Form::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return {ModelName}Infolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return {ModelName}sTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => List{ModelName}s::route('/'),
            'create' => Create{ModelName}::route('/create'),
            'view'   => View{ModelName}::route('/{record}'),
            'edit'   => Edit{ModelName}::route('/{record}/edit'),
        ];
    }
}
```

---

## Step 3 – Buat Schema Form (`Schemas/{ModelName}Form.php`)

```php
<?php

namespace App\Filament\Resources\{ModelName}s\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class {ModelName}Form
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Section Label')
                ->columns(2)
                ->schema([
                    // Form components di sini
                    // TextInput, Select, DatePicker, FileUpload, etc.
                ]),
        ]);
    }
}
```

**Aturan form:**

- Gunakan `->visible(fn () => auth()->user()->hasRole([...]))` untuk field yang hanya tampil ke role tertentu.
- Field virtual (tidak ada kolom di DB) wajib pakai `->dehydrated(false)`.
- Gunakan `->relationship()` untuk Select yang terhubung relasi.

---

## Step 4 – Buat Infolist (`Schemas/{ModelName}Infolist.php`)

```php
<?php

namespace App\Filament\Resources\{ModelName}s\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class {ModelName}Infolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Section Label')
                ->schema([
                    TextEntry::make('field_name')->label('Label'),
                ]),
        ]);
    }
}
```

---

## Step 5 – Buat Table (`Tables/{ModelName}sTable.php`)

```php
<?php

namespace App\Filament\Resources\{ModelName}s\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class {ModelName}sTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('field_name')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

---

## Step 6 – Tambah Row-Level Authorization (jika perlu)

Jika resource perlu filter berdasarkan role, tambahkan `getEloquentQuery()` di Resource:

```php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if ($user->hasRole(['super_admin', 'admin', 'sekertaris'])) {
        return $query;
    }

    return $query->where('user_id', $user->id);
}
```

---

## Step 7 – Generate Shield Permissions

```bash
php artisan filament:shield-generate --no-interaction
```

Ini auto-generate permissions seperti `ViewAny:{ModelName}`, `Create:{ModelName}`, dll.

---

## Step 8 – Buat/Update Policy

```bash
php artisan make:policy {ModelName}Policy --model={ModelName} --no-interaction
```

Ikuti pattern dari `ProtocolPolicy.php`:

```php
<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\{ModelName};
use Illuminate\Auth\Access\HandlesAuthorization;

class {ModelName}Policy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:{ModelName}');
    }

    public function view(AuthUser $authUser, {ModelName} $model): bool
    {
        return $authUser->can('View:{ModelName}');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:{ModelName}');
    }

    public function update(AuthUser $authUser, {ModelName} $model): bool
    {
        return $authUser->can('Update:{ModelName}');
    }

    public function delete(AuthUser $authUser, {ModelName} $model): bool
    {
        return $authUser->can('Delete:{ModelName}');
    }
}
```

---

## Step 9 – Format Kode

```bash
vendor/bin/pint --dirty
```

---

## Step 10 – Tulis & Jalankan Test

```bash
php artisan make:test {ModelName}ResourceTest --no-interaction
php artisan test --filter={ModelName}ResourceTest
```

Lihat skill `write-feature-test` untuk template test lengkap.

---

## Catatan Penting

- Selalu gunakan `Filament\Support\Icons\Heroicon` enum untuk icon, bukan string `'heroicon-o-...'`.
- Semua action ada di `Filament\Actions\*` – tidak ada `Filament\Tables\Actions\*` di v4.
- Schema layout components (`Section`, `Grid`, `Fieldset`) ada di `Filament\Schemas\Components\`.
- Form input components (`TextInput`, `Select`, dll.) tetap di `Filament\Forms\Components\`.
