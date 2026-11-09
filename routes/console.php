<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:make {email} {password=password123}', function (string $email, string $password) {
    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => explode('@', $email)[0],
            'password' => Hash::make($password),
            'role' => 'admin',
            'admin_level' => 'super_admin',
        ]
    );

    $this->info("Admin account [{$user->email}] successfully created/updated with super_admin access!");
})->purpose('Create or promote an account to super_admin with a designated password');
