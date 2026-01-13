<?php

namespace Tests\Feature;


use App\Filament\Resources\Protocols\Pages\ViewProtocol;
use App\Mail\ProtocolSubmittedMail;
use App\Mail\ReviewAssignmentMail;
use App\Mail\ReviewSubmittedMail;
use App\Models\Protocol;
use App\Models\ReviewerKelompok;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProtocolNotificationTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Setup Role Permissions
        // Gunakan firstOrCreate agar aman
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'reviewer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sekertaris', 'guard_name' => 'web']);
    }
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    #[Test]
    public function it_sends_notification_and_email_to_admin_when_protocol_is_created()
    {
        // A. Setup
        Mail::fake(); // Mencegah email asli terkirim

        $admin = User::factory()->create(['email' => 'yohanes.wisnu33@gmail.com']);
        $admin->assignRole('admin');

        $peneliti = User::factory()->create();
        $peneliti->assignRole('user');

        // B. Act: Peneliti submit protokol (Trigger Observer 'created')
        $protocol = Protocol::create([
            'user_id' => $peneliti->id,
            'perihal_pengajuan' => 'Penelitian Test Malaria',
            'jenis_protocol' => 'Manusia',
            'tanggal_pengajuan' => Carbon::now(),
            'uploadpernyataan' => 'uploadpernyataan/3..docx',
            'buktipembayaran' => 'buktipembayaran/Screenshot 2025-08-11 145740.png'

            // isi field lain sesuai kebutuhan database Anda (nullable/required)
        ]);

        // C. Assert (Verifikasi)

        // 1. Cek Email terkirim ke Admin
        Mail::assertQueued(ProtocolSubmittedMail::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });

        // 2. Cek Notifikasi Database Admin
        $this->assertCount(1, $admin->notifications);
        $this->assertEquals('Pengajuan Protokol Baru', $admin->notifications->first()->data['title']);
    }

    #[Test]
    public function it_sends_email_to_reviewer_when_assigned_to_group()
    {
        // A. Setup
        Mail::fake();

        // Buat Reviewer dalam Kelompok ID 1
        $kelompok = ReviewerKelompok::create([
            'nama_kelompok' => 'Kelompok Test',
            'is_active' => 1 // Sesuaikan dengan kolom di DB Anda
        ]);

        // Baru buat User dengan ID kelompok yang valid
        $reviewer = User::factory()->create([
            'reviewer_kelompok_id' => $kelompok->id,
            'email' => 'rev@test.com'
        ]);
        $reviewer->assignRole('reviewer');

        $protocol = Protocol::factory()->create();

        // Assign protokol ke kelompok yang sama
        $protocol->update([
            'reviewer_kelompok_id' => $kelompok->id
        ]);

        // 1. Cek Email Reviewer
        Mail::assertQueued(ReviewAssignmentMail::class, function ($mail) use ($reviewer) {
            return $mail->hasTo($reviewer->email);
        });

        // 2. Cek Notifikasi Database Reviewer
        $this->assertCount(1, $reviewer->notifications);
    }

    // /** @test */
    #[Test]
    public function it_sends_email_to_researcher_when_reviewer_submits_review_via_filament_action()
    {
        // A. Setup
        Mail::fake();

        // 1. Buat Kelompok
        $kelompok = ReviewerKelompok::create([
            'nama_kelompok' => 'Kelompok A',
            'is_active' => 1,
        ]);

        // 2. Buat Peneliti
        $peneliti = User::factory()->create(['email' => 'peneliti@example.com']);
        $peneliti->assignRole('user');

        // 3. Buat Reviewer (Satu kali saja, jangan ditimpa!)
        $reviewer = User::factory()->create([
            'reviewer_kelompok_id' => $kelompok->id, // Masukkan ke kelompok
        ]);
        $reviewer->assignRole('reviewer'); // PERBAIKAN: Wajib assign role agar lolos Policy

        // 4. Buat Protokol (Satu kali saja)
        $protocol = Protocol::factory()->create([
            'user_id' => $peneliti->id,
            'reviewer_kelompok_id' => $kelompok->id, // ID Kelompok harus sama dengan reviewer
        ]);

        // B. Act: Livewire Test
        Livewire::actingAs($reviewer)
            ->test(ViewProtocol::class, ['record' => $protocol->getKey()])
            ->assertSuccessful() // Pastikan status 200 OK (Tidak 403)
            ->mountAction('addReview')
            ->setActionData([
                'comment' => '<p>Protokol ini bagus.</p>', // Input form
            ])
            ->callMountedAction()
            ->assertNotified('Review berhasil disimpan & Notifikasi dikirim');

        // C. Assert

        // 1. Cek Database
        // PERBAIKAN: Cocokkan comment dengan yang diinput di atas ('Protokol ini bagus.')
        $this->assertDatabaseHas('reviews', [
            'protocol_id' => $protocol->id,
            'user_id' => $reviewer->id,
            'comment' => '<p>Protokol ini bagus.</p>',
        ]);

        // 2. Cek Email terkirim ke Peneliti
        // Disini baru benar menggunakan ReviewSubmittedMail
        Mail::assertQueued(ReviewSubmittedMail::class, function ($mail) use ($peneliti) {
            return $mail->hasTo($peneliti->email);
        });
        // // A. Setup
        // Mail::fake();

        // $peneliti = User::factory()->create(['email' => 'peneliti@example.com']);
        // $peneliti->assignRole('user');
        // $reviewer = User::factory()->create();
        // $reviewer->assignRole('reviewer');

        // $kelompok = ReviewerKelompok::create([
        //     'nama_kelompok' => 'Kelompok A',
        //     'is_active' => 1,
        //     // isi kolom lain jika ada yang mandatory (wajib diisi)
        // ]);



        // $protocol = Protocol::factory()->create([
        //     'user_id' => $peneliti->id
        // ]);

        // $reviewer = User::factory()->create([
        //     'reviewer_kelompok_id' => $kelompok->id,
        // ]);

        // $protocol = Protocol::factory()->create([
        //     'user_id' => $peneliti->id,
        //     'reviewer_kelompok_id' => $kelompok->id,
        // ]);

        // // $reviewer->assignRole('reviewer');

        // // B. Act: Simulasi klik tombol Action di Halaman Filament
        // // Kita menggunakan Livewire test karena logic email ada di dalam Action::make()->action(...)

        // Livewire::actingAs($reviewer)
        //     ->test(ViewProtocol::class, ['record' => $protocol->getKey()])

        //     // 1. Pastikan halaman berhasil dimuat dulu
        //     ->assertSuccessful()

        //     // 2. Buka Modal Action (Mount)
        //     ->mountAction('addReview')

        //     // 3. Isi Data Form di dalam Modal
        //     ->setActionData([
        //         'comment' => 'Protokol ini bagus.',
        //     ])

        //     // 4. Submit Action (Klik tombol simpan di modal)
        //     ->callMountedAction()

        //     // 5. Assert notifikasi
        //     ->assertNotified('Review berhasil disimpan & Notifikasi dikirim');

        // // 1. Cek Review masuk database
        // $this->assertDatabaseHas('reviews', [
        //     'protocol_id' => $protocol->id,
        //     'user_id' => $reviewer->id,
        //     'comment' => 'Protokol ini sudah cukup bagus, namun perlu perbaikan metode.',
        // ]);

        // // 2. Cek Email terkirim ke Peneliti
        // Mail::assertQueued(ReviewSubmittedMail::class, function ($mail) use ($peneliti) {
        //     return $mail->hasTo($peneliti->email);
        // });
    }
}
