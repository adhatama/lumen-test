<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->truncate();

        DB::table('users')->insert([
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'password' => app('hash')->make('secret'),
            ],
        ]);
    }
}
