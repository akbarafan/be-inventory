<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@smklabschool.sch.id'],
            [
                'name'     => 'admin',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'petugas@smklabschool.sch.id'],
            [
                'name'     => 'petugas',
                'password' => Hash::make('user123'),
                'role'     => 'user',
            ]
        );
    }
}
