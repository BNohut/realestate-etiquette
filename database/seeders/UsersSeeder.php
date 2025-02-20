<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
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
                'name' => 'Süper',
                'last_name' => 'Yönetici',
                'email' => "superadmin@admin.com",
                'password' => bcrypt('123123'),
                'visibility' => 1,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Yönetici',
                'last_name' => "Örnek",
                'email' => "admin@admin.com",
                'password' => bcrypt('123123'),
                'visibility' => 1,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ofis Yöneticisi',
                'last_name' => 'Örnek',
                'email' => "yonetici@yonetici.com",
                'password' => bcrypt('123123'),
                'visibility' => 1,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ofis Danışmanı',
                'last_name' => 'Örnek',
                'email' => "ofisdanisman@ofis.com",
                'password' => bcrypt('123123'),
                'visibility' => 1,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Bireysel Danışman',
                'last_name' => 'Örnek',
                'email' => "danisman@danisman.com",
                'password' => bcrypt('123123'),
                'visibility' => 1,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ofis Asistanı',
                'last_name' => 'Örnek',
                'email' => "asistan@asistan.com",
                'password' => bcrypt('123123'),
                'visibility' => 1,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
