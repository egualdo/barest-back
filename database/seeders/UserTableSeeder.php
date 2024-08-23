<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        User::truncate();
        DB::table('model_has_roles')->where('model_type', 'App\Models\User')->truncate();
        DB::table('model_has_permissions')->where('model_type', 'App\Models\User')->truncate();
        
        $user = User::create([
            'username' => 'Company Limited',            
            'cif_nif' => 'D20862189',
            'email' => 'company@gmail.com',
            'email_verified_at' => now(),
            'password' => '123456789', // password
            'remember_token' => Str::random(10),
            'picture'=> null,            
            'profession'=>null,'city'=>null
        ]);
        $user->assignRole('company');

        $user = User::create([
            'username' => 'Eduardo Colmenares',            
            'email' => 'colmenares203@mailinator.com',
            'email_verified_at' => now(),
            'password' => '123456789', // password
            'remember_token' => Str::random(10),
            'picture'=> null,            
            'profession'=>null,'city'=>null
        ]);
        $user->assignRole('user');

        $user = User::create([
            'username' => 'Carlos Silva',            
            'email' => 'carlossilva@mailinator.com',
            'email_verified_at' => now(),
            'password' => '123456789', // password
            'remember_token' => Str::random(10),
            'picture'=> null,            
            'profession'=>null,'city'=>null
        ]);
        $user->assignRole('user');

        $user = User::create([
            'username' => 'Conni Duarte',            
            'email' => 'conniduarte@mailinator.com',
            'email_verified_at' => now(),
            'password' => '123456789', // password
            'remember_token' => Str::random(10),
            'picture'=> null,            
            'profession'=>null,'city'=>null
        ]);
        $user->assignRole('user');

        $user = User::create([
            'username' => 'Ikemi Duarte',            
            'email' => 'ikemiduarte@mailinator.com',
            'email_verified_at' => now(),
            'password' => '123456789', // password
            'remember_token' => Str::random(10),
            'picture'=> null,            
            'profession'=>null,'city'=>null
        ]);
        $user->assignRole('user');
        
        $users = User::factory(20)->create();
        /* $users = User::select()->Leftjoin('model_has_roles', 'users.id', 'model_has_roles.model_id')
                            ->where('model_has_roles.model_id', NULL)
                            ->get(); */
        
        foreach($users as $user){
            $user->assignRole( !is_null($user->company_name) ? 'company' : 'user' );
        } 
    }
}
