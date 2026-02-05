<?php

namespace App\Http\Responses;


use Auth;
use Filament\Auth\Http\Responses\LoginResponse as BaseLoginResponse;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        // dd('BERHASIL! File ini dibaca oleh sistem.');

        $user = Filament::auth()->user();
        // dd($user->getRoleNames()->toArray());

        // 1. Prioritas Admin: Jika punya role Admin/Super Admin, lempar ke Panel Admin
        if ($user->hasRole(['admin', 'sekertaris'])) {
            return redirect()->to(Filament::getPanel('admin')->getUrl());
        }

        // 2. Prioritas Reviewer: Jika dia Reviewer, lempar ke Panel Reviewer
        if ($user->hasRole('reviewer')) {
            return redirect()->to(Filament::getPanel('reviewer')->getUrl());
        }

        // 3. Prioritas User/Peneliti: Jika user biasa, lempar ke Panel User
        if ($user->hasRole('user')) {
            return redirect()->to(Filament::getPanel('user')->getUrl());
        }

        // 4. Default Fallback (Jaga-jaga jika tidak punya role diatas)
        // Kita kembalikan ke panel default yang sedang aktif atau halaman home
        // return redirect()->to(Filament::getCurrentPanel()->getUrl());
        // 4. Fallback ke logika asli Filament (agar support redirect intended url)
        return parent::toResponse($request);
    }
}
