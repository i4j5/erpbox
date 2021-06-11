<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\DocumentTemplateVariableTypeSeeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(RolesAndPermissionsSeeder::class);
		$this->command->info('Роли и права пользователей созданы!');

        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.admin',
            'password' => Hash::make('admin'),
        ]);

        $user->assignRole('super-admin');
        $this->command->info('Админ создан. email: admin@admin.admin password: admin');

        $this->call(DocumentTemplateVariableTypeSeeder::class);
    }
}
