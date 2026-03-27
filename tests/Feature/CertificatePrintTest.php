<?php

namespace Tests\Feature;

use App\Models\Protocol;
use App\Models\StatusReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CertificatePrintTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $owner;

    protected User $otherUser;

    protected Protocol $protocol;

    protected StatusReview $exemptedStatus;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sekertaris',  'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'reviewer',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user',        'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->owner = User::factory()->create();
        $this->owner->assignRole('user');

        $this->otherUser = User::factory()->create();
        $this->otherUser->assignRole('user');

        $this->exemptedStatus = StatusReview::create(['status_name' => 'Exempted']);

        $this->protocol = Protocol::factory()->create([
            'user_id'   => $this->owner->id,
            'status_id' => $this->exemptedStatus->id,
        ]);
    }

    // ==========================================
    // TEST: Pemilik protokol Exempted dapat akses
    // ==========================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function owner_can_access_certificate_of_their_exempted_protocol(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('certificates.protocol', [
            'protocol' => $this->protocol->id,
            'nama'     => 'John Doe',
        ]));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee($this->protocol->perihal_pengajuan);
    }

    // ==========================================
    // TEST: Admin dapat akses certificate siapapun
    // ==========================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_access_certificate_of_any_exempted_protocol(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('certificates.protocol', [
            'protocol' => $this->protocol->id,
            'nama'     => 'Admin Nama',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Admin Nama');
    }

    // ==========================================
    // TEST: User lain tidak bisa akses certificate milik orang lain
    // ==========================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function other_user_cannot_access_certificate_belonging_to_someone_else(): void
    {
        $this->actingAs($this->otherUser);

        $response = $this->get(route('certificates.protocol', [
            'protocol' => $this->protocol->id,
            'nama'     => 'Hacker',
        ]));

        $response->assertStatus(403);
    }

    // ==========================================
    // TEST: Protokol non-Exempted tidak bisa dicetak
    // ==========================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function certificate_cannot_be_printed_for_non_exempted_protocol(): void
    {
        $fastReviewStatus = StatusReview::create(['status_name' => 'Fast Review']);

        $nonExemptedProtocol = Protocol::factory()->create([
            'user_id'   => $this->owner->id,
            'status_id' => $fastReviewStatus->id,
        ]);

        $this->actingAs($this->owner);

        $response = $this->get(route('certificates.protocol', [
            'protocol' => $nonExemptedProtocol->id,
            'nama'     => 'John Doe',
        ]));

        $response->assertStatus(403);
    }

    // ==========================================
    // TEST: Guest (belum login) diredirect ke login
    // ==========================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('certificates.protocol', [
            'protocol' => $this->protocol->id,
            'nama'     => 'Guest',
        ]));

        $response->assertRedirect('/admin/login');
    }

    // ==========================================
    // TEST: nama_lengkap query default ke user->name jika kosong
    // ==========================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function nama_lengkap_defaults_to_owner_name_when_not_provided(): void
    {
        $this->actingAs($this->owner);

        // Tanpa query ?nama=...
        $response = $this->get(route('certificates.protocol', $this->protocol->id));

        $response->assertStatus(200);
        // Default ke nama user yang sedang login
        $response->assertSee($this->owner->name);
    }
}
