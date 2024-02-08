<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                // Auth
                'username'      => 'dr4v',
                'email'         => 'erikdltr@gmail.com',
                'password'      => 'secretos',
                'first_name'    => 'Erik',
                'last_name'     => 'Ramirez',
                'is_admin'      =>  true,
            ]
        ];

        foreach($users as $user)
        {
            User::create($user);
        }
    }
}
