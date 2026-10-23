<?php

namespace Tests\Feature;

use App\Filament\Resources\Protocols\Pages\EditProtocol;
use App\Filament\Resources\Protocols\RelationManagers\DocumentRelationManager;
use App\Models\Document;
use App\Models\Protocol;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DocumentRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Protocol $protocol;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        // Setup Roles
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'reviewer', 'guard_name' => 'web']);

        foreach (['ViewAny:Protocol', 'View:Protocol'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->admin->syncPermissions(['ViewAny:Protocol', 'View:Protocol']);

        $this->protocol = Protocol::factory()->create();
    }

    #[Test]
    public function it_can_render_document_relation_manager_table()
    {
        $document = Document::create([
            'namadokumen' => 'Test Document',
            'jenisdokumen' => 'pdf',
            'path' => 'dokumen_pendukung/test.pdf',
            'user_id' => $this->admin->id,
            'protocol_id' => $this->protocol->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(DocumentRelationManager::class, [
            'ownerRecord' => $this->protocol,
            'pageClass' => EditProtocol::class,
        ])
            ->assertCanSeeTableRecords([$document]);
    }

    #[Test]
    public function it_can_download_document()
    {
        Storage::fake('public');
        Storage::disk('public')->put('dokumen_pendukung/test.pdf', 'dummy content');

        $document = Document::create([
            'namadokumen' => 'Test Document',
            'jenisdokumen' => 'pdf',
            'path' => 'dokumen_pendukung/test.pdf',
            'user_id' => $this->admin->id,
            'protocol_id' => $this->protocol->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(DocumentRelationManager::class, [
            'ownerRecord' => $this->protocol,
            'pageClass' => EditProtocol::class,
        ])
            ->callAction(TestAction::make('download')->table($document))
            ->assertFileDownloaded('test.pdf', 'dummy content');
    }

    #[Test]
    public function assigned_reviewer_can_view_document_revision_tab_without_document_permission()
    {
        $reviewer = User::factory()->create();
        $reviewer->assignRole('reviewer');
        $this->protocol->reviewers()->attach($reviewer->id, [
            'role_in_review' => 'Ketua',
            'feedback_status' => 'pending',
        ]);

        $this->actingAs($reviewer);

        $this->assertTrue(DocumentRelationManager::canViewForRecord($this->protocol, EditProtocol::class));
    }

    #[Test]
    public function unassigned_reviewer_cannot_view_document_revision_tab()
    {
        $reviewer = User::factory()->create();
        $reviewer->assignRole('reviewer');

        $this->actingAs($reviewer);

        $this->assertFalse(DocumentRelationManager::canViewForRecord($this->protocol, EditProtocol::class));
    }
}
