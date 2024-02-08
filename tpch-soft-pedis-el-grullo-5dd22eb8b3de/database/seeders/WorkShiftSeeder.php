<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkShift;

class WorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['name' => 'Matutino'],
            ['name' => 'Vespertino'],
            ['name' => 'Nocturno'],
            ['name' => 'Mixto']
        ];

        foreach($users as $user)
        {
            WorkShift::create($user);
        }
    }
}
