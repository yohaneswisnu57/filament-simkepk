# SIKEPK - Project Context for AI Agents

**Project Name:** SIKEPK (Sistem Informasi Kelayakan Protokol Etik)  
**Framework:** Laravel 12 + Filament v4  
**Language:** PHP 8.4.1  
**Database:** SQLite (default) / MySQL  
**Frontend:** Tailwind CSS v4, Livewire v3, Alpine.js  
**Build Tools:** Vite  

---

## 1. PROJECT OVERVIEW

SIKEPK adalah sistem manajemen protokol etik berbasis web. Aplikasi ini memungkinkan pengguna untuk:
- Mengajukan protokol etik untuk penelitian
- Melakukan review terhadap protokol yang diajukan
- Mengelola dokumen pendukung
- Mengelola tim reviewer (kelompok reviewer)
- Tracking status review protokol
- Role-based access control (RBAC) dengan Spatie Permission

**Key Features:**
- Multi-level authorization dengan roles: super_admin, admin, sekertaris, reviewer, user
- Soft delete untuk protokol dan data sensitif
- File upload untuk dokumen pendukung
- Status tracking untuk progress review
- Notifikasi database untuk user activity
- Observer pattern untuk Protocol model events

---

## 2. DATABASE SCHEMA & MODELS

### Models Structure

#### **User Model** (`app/Models/User.php`)
```
Relationships:
- protocols() → hasMany(Protocol)
- reviewerKelompok() → belongsTo(ReviewerKelompok)
- reviews() → hasMany(Review)

Key Methods:
- isKetuaDariKelompok(kelompokId): bool
  → Cek apakah user adalah ketua dari kelompok reviewer tertentu

Traits:
- HasFactory, Notifiable, HasRoles (Spatie), HasDatabaseNotifications
```

**Fields:**
- id, name, email, password, email_verified_at, remember_token, reviewer_kelompok_id
- timestamps, softDeletes (implicit via related models)

---

#### **Protocol Model** (`app/Models/Protocol.php`)
```
Relationships:
- statusReview() → belongsTo(StatusReview, 'status_id')
- User() → belongsTo(User, 'user_id')
- document() → hasMany(Document, 'protocol_id')
- reviews() → hasMany(Review, 'protocol_id')
- assignedReviewerKelompok() → belongsTo(ReviewerKelompok, 'reviewer_kelompok_id')

Observers:
- ProtocolObserver → Handles events when protocol is created/updated/deleted

Traits:
- SoftDeletes
```

**Fields:**
- id, perihal_pengajuan (concern), jenis_protocol (Manusia/Hewan)
- tanggal_pengajuan (submission date), status_id (FK to StatusReview)
- uploadpernyataan (statement file), buktipembayaran (proof of payment file)
- user_id (FK to User), reviewer_kelompok_id (FK to ReviewerKelompok)
- tgl_mulai_review, tgl_selesai_review (review timeline)
- timestamps, softDeletes

---

#### **ReviewerKelompok Model** (`app/Models/ReviewerKelompok.php`)
```
Relationships:
- users() → hasMany(User, 'reviewer_kelompok_id')
- assignedProtocols() → hasMany(Protocol, 'reviewer_kelompok_id')
- anggota() → hasMany(User, 'reviewer_kelompok_id') [alias for users]
- ketua() → belongsTo(User, 'ketua_user_id') [group leader]

Traits:
- SoftDeletes
```

**Fields:**
- id, kelompok_reviewer (name), is_active (boolean)
- ketua_user_id (FK to User - group leader)
- created_by (string - who created this group)
- timestamps, softDeletes

---

#### **Review Model** (`app/Models/Review.php`)
```
Relationships:
- protocol() → belongsTo(Protocol)
- user() → belongsTo(User)

Fillable:
- protocol_id, user_id, comment
```

**Fields:**
- id, protocol_id (FK), user_id (FK), comment (text review)
- timestamps

---

#### **Document Model** (`app/Models/Document.php`)
```
Relationships:
- protocol() → belongsTo(Protocol, 'protocol_id')
- user() → belongsTo(User, 'user_id')

Traits:
- SoftDeletes
```

**Fields:**
- id, namadokumen, jenisdokumen
- user_id (FK), protocol_id (FK)
- timestamps, softDeletes

---

#### **StatusReview Model** (`app/Models/StatusReview.php`)
```
Traits:
- SoftDeletes
```

**Fields:**
- id, status_name (e.g., Pending, In Review, Approved, Rejected)
- timestamps, softDeletes

---

#### **MemberReviewer Model** (`app/Models/MemberReviewer.php`)
```
Table: member_reviewers
Guardable - all fields are fillable
```

---

## 3. FILAMENT RESOURCES ARCHITECTURE

### Resource Organization

Resources follow Filament v4 structure with custom Schemas:

```
app/Filament/Resources/
├── Protocols/
│   ├── ProtocolResource.php
│   ├── Pages/
│   │   ├── ListProtocols.php
│   │   ├── CreateProtocol.php
│   │   ├── EditProtocol.php
│   │   └── ViewProtocol.php
│   ├── RelationManagers/
│   │   ├── DocumentRelationManager.php
│   │   └── ReviewsRelationManager.php
│   ├── Schemas/
│   │   ├── ProtocolForm.php
│   │   └── ProtocolInfolist.php
│   ├── Tables/
│   │   └── ProtocolsTable.php
│   └── Widgets/
│       └── StatsOverview.php
├── Users/
│   ├── UserResource.php
│   ├── Pages/
│   ├── Schemas/
│   │   ├── UserForm.php
│   │   └── UserInfolist.php
│   └── Tables/
│       └── UsersTable.php
├── ReviewerKelompoks/
├── StatusReviews/
└── Documents/
```

### Authorization in Queries

**ProtocolResource::getEloquentQuery()** implements role-based filtering:

```php
// super_admin, admin, sekertaris → See all protocols
if ($user->hasRole(['super_admin', 'admin', 'sekertaris'])) {
    return $query;
}

// Regular users/reviewers:
// - See their own submitted protocols
// - See protocols assigned to their reviewer group
$query->where(function (Builder $q) use ($user, $userReviewerKelompokId) {
    $q->where('user_id', $user->id);
    if ($userReviewerKelompokId) {
        $q->orWhere('reviewer_kelompok_id', $userReviewerKelompokId);
    }
});
```

---

## 4. AUTHORIZATION & PERMISSIONS

### Policy-Based Access Control

All resources use Filament Shield + Spatie Permission:

**File Structure:**
```
app/Policies/
├── ProtocolPolicy.php
├── UserPolicy.php
├── DocumentPolicy.php
├── ReviewerKelompokPolicy.php
├── StatusReviewPolicy.php
├── RolePolicy.php
└── (others)
```

**Permission Pattern:**
Each policy checks permission like: `ViewAny:Model`, `View:Model`, `Create:Model`, `Update:Model`, `Delete:Model`, `Restore:Model`, `ForceDelete:Model`, etc.

**Example (ProtocolPolicy):**
```php
public function viewAny(AuthUser $user): bool {
    return $user->can('ViewAny:Protocol');
}

public function create(AuthUser $user): bool {
    return $user->can('Create:Protocol');
}

public function update(AuthUser $user, Protocol $protocol): bool {
    return $user->can('Update:Protocol');
}
```

### Role Types

- **super_admin**: Full access to all resources
- **admin**: Full access to all resources + settings
- **sekertaris**: Can view/manage protocols and assignments
- **reviewer**: Can view assigned protocols and submit reviews
- **user**: Can submit and view own protocols

---

## 5. KEY FEATURES EXPLAINED

### 5.1 Protocol Submission Flow

1. **User** creates new Protocol via CreateProtocol page
   - Fills: perihal_pengajuan, jenis_protocol, tanggal_pengajuan
   - Uploads: uploadpernyataan, buktipembayaran
   - auto-set: user_id = auth()->id()

2. **Admin/Sekertaris** can:
   - Set status_id (from StatusReview)
   - Assign reviewer_kelompok_id
   - Set tgl_mulai_review and tgl_selesai_review

3. **Reviewer Group** members:
   - See assigned protocols
   - Submit reviews (create Review records)

4. **Document Management:**
   - Users can upload multiple documents per protocol
   - Each document linked to Protocol via DocumentRelationManager

### 5.2 File Upload & Storage

**Upload Configuration:**
```php
FileUpload::make('uploadpernyataan')
    ->disk('public')
    ->directory('uploadpernyataan')
    ->acceptedFileTypes(['application/pdf', 'application/msword', ...])
    ->maxSize(3072) // 3MB limit
    ->preserveFilenames()
```

**Files stored in:** `storage/app/public/uploadpernyataan/` and `storage/app/public/buktipembayaran/`

### 5.3 Status Review System

**StatusReview** is a simple lookup table:
- id, status_name, timestamps, soft_deletes

Example status values: Pending, In Review, Under Revision, Approved, Rejected

### 5.4 Reviewer Group Assignment

**ReviewerKelompok:**
- Contains multiple users (anggota)
- Has one ketua (leader/head reviewer)
- Can be assigned to Protocol for review task distribution
- is_active flag to enable/disable groups

---

## 6. FORM & TABLE SCHEMAS (Custom Implementation)

### Form Schema Pattern

**Location:** `Schemas/` directories (e.g., `ProtocolForm.php`)

**Structure:**
```php
class ProtocolForm {
    public static function configure(Schema $schema): Schema {
        return $schema->components([
            Section::make('Information Protocol')->schema([...]),
            Section::make('Review Timeline')->schema([...]),
            Section::make('Supporting Files')->schema([...]),
        ]);
    }
}
```

**Key Form Components Used:**
- TextInput, Select, DatePicker, FileUpload
- Section (layout), Fieldset (grouping)
- Conditional visibility with `->visible(fn () => ...)`
- Conditional hydration with `->dehydrated(fn ($operation) => ...)`

### Table Schema Pattern

**Location:** `Tables/` directories (e.g., `ProtocolsTable.php`)

Tables typically include:
- TextColumn for data display
- ActionGroup for bulk/individual actions
- Filters for searching
- Pagination

---

## 7. OBSERVERS & EVENTS

### ProtocolObserver

**File:** `app/Observers/ProtocolObserver.php`

Attached to Protocol model via attribute:
```php
#[ObservedBy(ProtocolObserver::class)]
class Protocol extends Model { ... }
```

**Common hooks:**
- created() - triggered when new protocol created
- updated() - triggered when protocol updated
- deleted() - triggered when protocol soft-deleted
- restored() - triggered when protocol restored

---

## 8. CONFIGURATION FILES

### Permission Configuration

**File:** `config/permission.php`

Sets up Spatie Permission tables:
- permissions
- roles
- model_has_permissions
- model_has_roles
- role_has_permissions

### Filament Shield Configuration

**File:** `config/filament-shield.php`

Auto-generates permissions for all resources/policies.

### Panel Configuration

**File:** `app/Providers/Filament/AdminPanelProvider.php`

Key settings:
- `->default()` - default panel
- `->spa()` - single page app mode
- `->path('admin')` - URL path prefix
- `->login()` - enable auth
- `->databaseNotifications()` - enable notifications
- `->globalSearch(false)` - DISABLED (header search bar removed)
- `->colors(['primary' => Color::Green])` - primary color
- `->plugins([FilamentShieldPlugin::make()])` - enable shield

---

## 9. TESTING APPROACH

### Test Structure

**Directories:**
```
tests/
├── Feature/
├── Unit/
└── TestCase.php (base class)
```

### Testing Conventions

**Feature Tests (Protocol-related):**
```php
// Authenticate
$user = User::factory()->create();
$this->actingAs($user);

// Test with Filament Livewire
livewire(ListProtocols::class)
    ->assertCanSeeTableRecords($protocols)
    ->searchTable('keyword')
    ->assertCanSeeTableRecords(...);

// Test Create/Update
livewire(CreateProtocol::class)
    ->fillForm(['perihal_pengajuan' => 'Test', ...])
    ->call('create')
    ->assertNotified()
    ->assertRedirect();
```

### Authorization Tests

```php
$user = User::factory()->create();
$user->givePermissionTo('ViewAny:Protocol');
// or
$user->assignRole('reviewer');
```

---

## 10. CODE CONVENTIONS

### PHP Standards

- **PHP 8.4 features:** Constructor property promotion, typed properties
- **Return Types:** All methods must have explicit return types
- **PHPDoc:** Use for array shapes, complex types
- **Control Structures:** Always use curly braces `{ }`
- **Constructor:** Never allow empty constructors

**Example:**
```php
public function __construct(public ProtocolRepository $repository) { }

public function storeProtocol(Protocol $protocol): bool {
    return $this->repository->save($protocol);
}
```

### Naming Conventions

- **Models:** Singular (User, Protocol)
- **Database Tables:** Plural (users, protocols)
- **Methods:** camelCase
- **Variables:** camelCase
- **Constants:** UPPER_SNAKE_CASE
- **Enums:** TitleCase (e.g., ReviewStatus, ProtocolType)

### File Organization

- **Models:** `app/Models/`
- **Policies:** `app/Policies/`
- **Observers:** `app/Observers/`
- **Form Requests:** `app/Http/Requests/` (if used)
- **Controllers:** `app/Http/Controllers/` (minimal - Filament handles most)
- **Resources:** `app/Filament/Resources/`
- **Schemas:** Within resource folders
- **Migrations:** `database/migrations/`
- **Factories:** `database/factories/`
- **Seeders:** `database/seeders/`

---

## 11. COMMON DEVELOPMENT TASKS

### Task 1: Adding a New Field to Protocol

1. **Create Migration:**
   ```bash
   php artisan make:migration add_new_field_to_protocols_table
   ```

2. **Update ProtocolForm.php:**
   ```php
   TextInput::make('new_field')
       ->label('New Field Label')
       ->required()
   ```

3. **Update ProtocolInfolist.php:**
   ```php
   TextEntry::make('new_field')
   ```

4. **Update ProtocolsTable.php:**
   ```php
   TextColumn::make('new_field')
   ```

5. **Run migration:**
   ```bash
   php artisan migrate
   ```

### Task 2: Adding New Role & Permissions

1. **Use Tinker or Seeder:**
   ```php
   $role = Role::create(['name' => 'new_role', 'guard_name' => 'web']);
   $permission = Permission::create(['name' => 'ViewAny:Protocol', 'guard_name' => 'web']);
   $role->givePermissionTo($permission);
   ```

2. **Assign to User:**
   ```php
   $user->assignRole('new_role');
   ```

3. **Filament Shield auto-generates for new resources:**
   ```bash
   php artisan filament:shield-generate
   ```

### Task 3: Creating New Resource (CRUD)

```bash
php artisan make:filament-resource NewModel --generate
```

This creates:
- Resource class
- List/Create/Edit/View pages
- Database operations

### Task 4: File Upload Handling

Files are stored in:
- Config: `config/filesystems.php`
- Public disk: `storage/app/public/`
- Access via: `asset('storage/uploadpernyataan/filename.pdf')`

---

## 12. DEPENDENCIES & VERSIONS

| Package | Version | Purpose |
|---------|---------|---------|
| Laravel | 12.x | Web framework |
| Filament | 4.x | Admin panel SDUI |
| Livewire | 3.x | Real-time reactivity |
| Spatie Permission | Latest | RBAC |
| Filament Shield | 4.x | Permission management |
| Tailwind CSS | 4.x | Styling |
| Alpine.js | v3 | Lightweight interactivity |
| Vite | 7.x | Build tool |
| PHP | 8.4+ | Language |

---

## 13. IMPORTANT NOTES FOR AI AGENTS

### Code Style

1. Run **Pint** before committing:
   ```bash
   vendor/bin/pint --dirty
   ```

2. Always check **sibling files** for conventions

3. Follow **existing code patterns** in the project

### Migration & Database

- Always disable foreign key constraints when creating/dropping tables with relationships
- Use soft deletes for sensitive data (Protocol, Document, etc.)
- When modifying columns, preserve all previous attributes

### Authorization

- Always check policies before allowing access
- Use `getEloquentQuery()` for row-level filtering
- Use `->visible()` in forms/tables to hide fields from unauthorized users

### Filament Specific

- Use `Schema` components instead of direct form components
- Use static `make()` and `configure()` methods
- Store schemas in separate `Schemas/` folders
- Test all features with Livewire testing

### Testing

- Use factories for model creation
- Always authenticate before testing protected resources
- Test role-based access control thoroughly
- Use `livewire()` helper for component testing

---

## 14. PROJECT ENTRY POINTS

### Web Routes
- **Admin Panel:** `/admin`
- **Login:** `/admin/login`
- **Home/Welcome:** `/`

### Key Pages
- **Protocols List:** `/admin/protocols`
- **Create Protocol:** `/admin/protocols/create`
- **Users Management:** `/admin/users`
- **Reviewer Groups:** `/admin/reviewer-kelompoks`
- **Status Review:** `/admin/status-reviews`
- **Documents:** Via Protocol relation

### API Endpoints
- Currently minimal - mostly Filament SPA handles interactions
- Future expansion would follow Laravel API conventions

---

## 15. HELPFUL COMMANDS

```bash
# Development
composer run dev                    # Run dev server + queue + logs + vite
php artisan serve                 # Start dev server
npm run dev                        # Watch Vite assets

# Database
php artisan migrate              # Run migrations
php artisan migrate:fresh        # Reset & re-run
php artisan db:seed              # Run seeders
php artisan tinker               # Interactive shell

# Cache & Optimization
php artisan config:clear         # Clear config cache
php artisan cache:clear          # Clear all cache
php artisan filament:optimize-clear  # Clear Filament cache

# Code Quality
vendor/bin/pint                  # Format code
php artisan test                 # Run tests
php artisan test --coverage      # With coverage report

# Asset Building
npm run build                    # Production build
npm run dev                      # Dev watch mode

# Filament Shield
php artisan filament:shield-generate  # Generate permissions
```

---

## SUMMARY

This is a **Laravel 12 + Filament v4** project for managing research ethics protocols (SIKEPK). It uses:

- **Role-based access control** with Spatie Permission
- **Soft-delete pattern** for data safety
- **Filament Admin Panel** as SDUI interface
- **Livewire v3** for real-time reactivity
- **Tailwind CSS v4** for styling
- **Custom Schema pattern** for forms/tables/infolists
- **Observer pattern** for model events
- **Policy-based authorization** for fine-grained control

When working with this project, **always**:
1. Follow existing code patterns
2. Check sibling files for conventions
3. Use Filament/Laravel best practices
4. Test authorization thoroughly
5. Run Pint before committing
6. Consider role-based filtering in queries
