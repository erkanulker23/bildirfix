<?php

use App\Support\SuperAdmin;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bildir:ensure-super-admin {--password=}', function () {
    try {
        $password = $this->option('password');
        $password = is_string($password) && $password !== '' ? $password : null;
        $user = SuperAdmin::syncAccount($password);
    } catch (\InvalidArgumentException $e) {
        $this->error($e->getMessage());

        return 1;
    }

    $this->info('Süper yönetici hazır: '.SuperAdmin::email()." (kullanıcı #{$user->id})");
    $this->line('Giriş: /superlogin veya /login');

    return 0;
})->purpose('Tanımlı süper yönetici hesabını oluşturur veya şifresini sıfırlar');

Schedule::command('bildir:import-external')->hourly();
