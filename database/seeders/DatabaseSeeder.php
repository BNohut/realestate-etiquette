<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Artisan::call('orchid:admin admin admin@admin.com 123123');
        Artisan::call('storage:link');

        $this->call(
            [
                ProvincesTableSeeder::class,
                StatesTableSeeder::class,
                RecordTypeSeeder::class,
                ConfigSeeder::class,
                RolesSeeder::class,
                UsersSeeder::class,
                RoleUserSeeder::class,
            ]

        );
    }
}
