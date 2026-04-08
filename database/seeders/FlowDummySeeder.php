<?php

namespace Database\Seeders;

use App\Models\Protocol;
use App\Models\Review;
use App\Models\User;
use App\Models\StatusReview;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FlowDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Siapkan User Dummy
        $pengusul = User::firstOrCreate(
            ['email' => 'pengusul@dummy.com'],
            [
                'name' => 'Dr. Dummy Pengusul',
                'password' => Hash::make('password'),
            ]
        );

        $reviewer1 = User::firstOrCreate(
            ['email' => 'reviewer1@dummy.com'],
            [
                'name' => 'Prof. Dummy Reviewer 1 (Ketua)',
                'password' => Hash::make('password'),
            ]
        );

        $reviewer2 = User::firstOrCreate(
            ['email' => 'reviewer2@dummy.com'],
            [
                'name' => 'Drs. Dummy Reviewer 2 (Sekertaris)',
                'password' => Hash::make('password'),
            ]
        );

        // Pastikan role (asumsi menggunakan Spatie Permissions)
        // $reviewer1->assignRole('reviewer'); 
        // $reviewer2->assignRole('reviewer');

        // ────────────────────────────────────────────────────────────────
        // SCENARIO A: TAHAP SUBMISSION (Baru diajukan)
        // ────────────────────────────────────────────────────────────────
        Protocol::create([
            'perihal_pengajuan' => '[DUMMY] Pengujian Klinis Obat Herbal A - Tahap Submission',
            'user_id' => $pengusul->id,
            'status_id' => 7, // SUBMISSION
            'jenis_protocol' => 'Baru',
            'contact_person' => '08123456789',
            'tanggal_pengajuan' => now()->subDays(2),
        ]);

        // ────────────────────────────────────────────────────────────────
        // SCENARIO B: TAHAP FAST REVIEW (Sedang Direview)
        // ────────────────────────────────────────────────────────────────
        $protocolB = Protocol::create([
            'perihal_pengajuan' => '[DUMMY] Studi Observasional Efek Diet B - Tahap Review',
            'user_id' => $pengusul->id,
            'status_id' => 6, // FAST REVIEW
            'reviewer_kelompok_id' => 1,
            'jenis_protocol' => 'Baru',
            'tanggal_pengajuan' => now()->subDays(10),
            'tgl_mulai_review' => now()->subDays(5),
        ]);

        // Assign Reviewers
        $protocolB->reviewers()->attach([
            $reviewer1->id => ['role_in_review' => 'Ketua', 'feedback_status' => 'pending'],
            $reviewer2->id => ['role_in_review' => 'Sekertaris', 'feedback_status' => 'pending'],
        ]);

        // ────────────────────────────────────────────────────────────────
        // SCENARIO C: TAHAP EXEMPTED (Selesai & Sertifikat)
        // ────────────────────────────────────────────────────────────────
        $protocolC = Protocol::create([
            'perihal_pengajuan' => '[DUMMY] Analisis Data Sekunder Rekam Medis C - Tahap Selesai',
            'user_id' => $pengusul->id,
            'status_id' => 1, // EXEMPTED
            'reviewer_kelompok_id' => 1,
            'jenis_protocol' => 'Review',
            'tanggal_pengajuan' => now()->subMonth(),
            'tgl_mulai_review' => now()->subWeeks(3),
            'tgl_selesai_review' => now()->subDays(2),
            'fast_review_decision' => 'Exempted',
        ]);

        // Assign Reviewers as Submitted
        $protocolC->reviewers()->attach([
            $reviewer1->id => ['role_in_review' => 'Ketua', 'feedback_status' => 'submitted'],
            $reviewer2->id => ['role_in_review' => 'Sekertaris', 'feedback_status' => 'submitted'],
        ]);

        // Add Reviews
        Review::create([
            'protocol_id' => $protocolC->id,
            'user_id' => $reviewer1->id,
            'comment' => 'Protokol ini memenuhi kriteria Exempted karena hanya menggunakan data sekunder anonim.',
            'verdict' => 'Exempted',
            'submitted_at' => now()->subDays(3),
        ]);
        $protocolC->comment("Protokol ini memenuhi kriteria Exempted karena hanya menggunakan data sekunder anonim.", $reviewer1);

        Review::create([
            'protocol_id' => $protocolC->id,
            'user_id' => $reviewer2->id,
            'comment' => 'Setuju. Risiko minimal.',
            'verdict' => 'Exempted',
            'submitted_at' => now()->subDays(2),
        ]);
        $protocolC->comment("Setuju. Risiko minimal.", $reviewer2);
    }
}
