<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Queue worker dijalankan secara real-time oleh systemd:
// systemctl status simkepk-queue
//
// Crontab tetap dipertahankan untuk schedule:run (task terjadwal lainnya):
// * * * * * /root/.config/herd-lite/bin/php /var/www/projectsimkepk-filament/artisan schedule:run >> /var/log/laravel-scheduler.log 2>&1
