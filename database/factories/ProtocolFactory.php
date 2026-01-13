<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Protocol>
 */
class ProtocolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Relasi otomatis membuat user baru jika tidak diisi
            'user_id' => User::factory(),

            // Isi dummy data sesuai kolom tabel protocols Anda
            'perihal_pengajuan' => $this->faker->sentence(10),
            'jenis_protocol' => $this->faker->sentence(5),
            'tanggal_pengajuan' => $this->faker->date(),
            'uploadpernyataan' => 'uploadpernyataan/' . $this->faker->word() . '.docx',
            'buktipembayaran' => 'buktipembayaran/' . $this->faker->word() . '.png',
            'tgl_mulai_review' => now(),
            'tgl_selesai_review' => now()->addDays(7),
            'status_id' => 1, // Sesuaikan dengan ID status default Anda
            // Tambahkan kolom lain yang NOT NULL di database Anda
        ];
    }
}
