<?php

namespace App\Livewire;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RoleSwitcher extends Component
{
    public $currentRole;

    public function mount()
    {
        // Set default value dropdown sesuai panel yang sedang aktif
        $this->currentRole = Filament::getCurrentPanel()->getId();
    }

    // Fungsi ini otomatis jalan saat dropdown berubah (karena wire:model.live)
    public function updatedCurrentRole($value)
    {
        $user = Auth::user();

        // 1. Logika Mapping: Panel ID -> URL
        // Sesuaikan 'admin', 'reviewer', 'user' dengan ID panel Anda
        $panels = [
            'admin' => filament()->getPanel('admin')->getUrl(),
            'reviewer' => filament()->getPanel('reviewer')->getUrl(),
            'user' => filament()->getPanel('user')->getUrl(),
        ];

        // 2. Cek apakah role yang dipilih valid & user punya akses
        // Kita manfaatkan method hasRole() dari Spatie
        // Pastikan nama key array ($value) sama dengan nama role di database

        if ($value === 'admin' && $user->hasRole(['super_admin', 'admin', 'sekertaris'])) {
            return redirect()->to($panels['admin']);
        }

        if ($value === 'reviewer' && $user->hasRole('reviewer')) {
            return redirect()->to($panels['reviewer']);
        }

        if ($value === 'user' && $user->hasRole('user')) {
            return redirect()->to($panels['user']);
        }

        // Jika user iseng pilih role yang dia tidak punya, kembalikan ke semula
        $this->currentRole = Filament::getCurrentPanel()->getId();

        // Opsional: Kirim notifikasi error
        // \Filament\Notifications\Notification::make()->title('Akses Ditolak')->danger()->send();
    }

    public function render()
    {
        // Ambil user untuk cek role apa saja yang dia punya
        // agar opsi yang muncul di dropdown HANYA role yang dia miliki
        $user = Auth::user();

        $options = [];

        if ($user->hasRole(['super_admin', 'admin', 'sekertaris'])) {
            $options['admin'] = 'Admin KEPK';
        }

        if ($user->hasRole('reviewer')) {
            $options['reviewer'] = 'Reviewer';
        }

        if ($user->hasRole('user')) {
            $options['user'] = 'Peneliti / User';
        }

        return view('livewire.role-switcher', [
            'options' => $options
        ]);
    }
}
