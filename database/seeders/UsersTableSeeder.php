<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'تعمیرکار',
            'email' => 'tech@example.com',
            'password' => Hash::make('password'),
            'role' => 'technician', 
        ]);

        User::create([
            'name' => 'پذیرش',
            'email' => 'recep@example.com',
            'password' => Hash::make('password'),
            'role' => 'reception',
        ]);

        User::create([
            'name' => 'تامین کننده',
            'email' => 'sup@example.com',
            'password' => Hash::make('password'),
            'role' => 'supply',
        ]);

        User::create([
            'name' => 'مدیریت',
            'email' => 'ceo@example.com',
            'password' => Hash::make('password'),
            'role' => 'ceo',
        ]);
        User::create([
            'name' => 'مجید محمد پور تعمیرکار',
            'email' => 'majid@site.com',
            'password' => bcrypt('majid123456'),
            'role' => 'technician'
        ]);
        User::create([
            'name' => 'ابراهیم غیبی دهناشی تعمیرکار',
            'email' => 'ebrahim@site.com',
            'password' => bcrypt('ebrahim123456'),
            'role' => 'technician'
        ]);
        User::create([
            'name' => 'اریا عالی پور تعمیرکار',
            'email' => 'arya@site.com',
            'password' => bcrypt('arya123456'),
            'role' => 'technician'
        ]);
        User::create([
            'name' => 'سینا رشتی تعمیرکار',
            'email' => 'sina@site.com',
            'password' => bcrypt('sina123456'),
            'role' => 'technician'
        ]);
    }
}
