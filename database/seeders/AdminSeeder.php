<?php

namespace Database\Seeders;

use App\Models\Sekbid;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->firstOrNew([
            'email' => 'admin@diskominfo.test',
        ]);

        $user->forceFill([
            'name' => 'Administrator',
            'nip' => '000000000000000000',
            'no_hp' => '081234567890',
            'jabatan' => 'Admin Inventaris',
            'sekbid_id' => Sekbid::query()->first()?->id,
            'role' => 'admin',
            'password' => Hash::make('password'),
        ])->save();
    }
}
