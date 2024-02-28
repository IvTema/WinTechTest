<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::updateOrCreate([
            'id' => 1
        ], [
            'name' => 'test admin',
            'email' => 'admin@admin.ru',
            'password' => Hash::make('password'),
        ]);
    }
}
