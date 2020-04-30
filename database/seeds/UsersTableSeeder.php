<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'password'       => '$2y$10$fmFJCS87GEbmbqxeiABGe.Wh5H5Frr6n6384VNc/AvyZYuRGWsj4y',
                'remember_token' => null,
            ],
        ];

        User::insert($users);

    }
}
