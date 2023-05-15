<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'User 1',
            'email' => 'user1@webtech.id',
            'password' => 'password1'
        ]);
        User::create([
            'name' => 'User 2',
            'email' => 'user2@webtech.id',
            'password' => 'password2'
        ]);
        User::create([
            'name' => 'User 3',
            'email' => 'user3@wordskills.org',
            'password' => 'password3'
        ]);
    }
}
