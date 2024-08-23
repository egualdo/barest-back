<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::truncate();
        //DB::table('model_has_roles')->where('model_type', 'App\Models\Admin')->truncate();
        //DB::table('model_has_permissions')->where('model_type', 'App\Models\Admin')->truncate();

        // create demo users
        $admin = Admin::create([
            'username' => 'ElyColmenares',
            'email' => 'colmenares203@barest.com',
            'password' => '123456789',
            'country_code' => '+58',
            'phone_number' => '1234567'
        ]);
        $admin->assignRole('admin');
        $admin->givePermissionTo('users management');

        $admin2 = Admin::create([
            'username' => 'AdminRole',
            'email' => 'admin@barest.com',
            'password' => '123456789',
            'country_code' => '+58',
            'phone_number' => '1234567'
        ]);
        $admin2->assignRole('admin');
        $admin2->givePermissionTo('total access');

        $admin3 = Admin::create([
            'username' => 'DominyelRivera',
            'email' => 'botlacrita617@barest.com',
            'password' => '123456789',
            'country_code' => '+58',
            'phone_number' => '1234567'
        ]);
        $admin3->assignRole('admin');
        $admin3->givePermissionTo('users management');
    }
}
