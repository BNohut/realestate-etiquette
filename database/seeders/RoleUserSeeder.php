<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;
use Orchid\Platform\Models\User;


class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::firstWhere('slug', 'super-yonetici');
        $user = User::firstWhere('name', 'Süper');
        $user->roles()->attach($role);

        $role = Role::firstWhere('slug', 'yonetici');
        $user = User::firstWhere('name', 'Yönetici');
        $user->roles()->attach($role);

        $role = Role::firstWhere('slug', 'ofis-yoneticisi');
        $user = User::firstWhere('name', 'Ofis Yöneticisi');
        $user->roles()->attach($role);

        $role = Role::firstWhere('slug', 'ofis-danismani');
        $user = User::firstWhere('name', 'Ofis Danışmanı');
        $user->roles()->attach($role);

        $role = Role::firstWhere('slug', 'bireysel-danisman');
        $user = User::firstWhere('name', 'Bireysel Danışman');
        $user->roles()->attach($role);

        $role = Role::firstWhere('slug', 'ofis-asistani');
        $user = User::firstWhere('name', 'Ofis Asistanı');
        $user->roles()->attach($role);
    }
}
