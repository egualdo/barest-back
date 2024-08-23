<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PetitionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('petitions')->truncate();
        DB::table('users')->update([
            'status' => 'ACTIVE',
            'block_reason' => ''
        ]);

        $users = User::get()->random(5);
        
        $users->each(function( $user ) {
            DB::table('petitions')->insert([
                                    'user_id' => $user->id,
                                    'text' => 'Solicito por favor que se me desbloquee mi cuenta.',
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
            
            $user->update([
                            'status' => 'BLOCKED',
                            'block_reason' => 'False information'
                        ]);
        });
    }
}
