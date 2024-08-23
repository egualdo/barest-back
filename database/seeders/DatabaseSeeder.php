<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Post;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {   
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');  
        $this->call(RandomLocationTableSeeder::class);   
        $this->call(PermissionsDemoSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(CategorySeed::class);
        $this->call(ConditionProductSeeder::class);
        $this->call(ContractTypeSeed::class);
        $this->call(ExperienceSeed::class);
        $this->call(IdiomSeeder::class);
        $this->call(ModalitySeed::class);
        $this->call(MotivesTableSeeder::class);
        $this->call(PaymentHistoryTableSeeder::class);
        $this->call(PaymentModalitySeed::class); 
        $this->call(PaymentTypeSeeder::class); 
        $this->call(PetitionsTableSeeder::class); 
        $this->call(PlanSeeder::class);
        $this->call(PostStageSeed::class);
        $this->call(PostTypeSeed::class);
        $this->call(PostSeed::class);
       
        $this->call(ProductTableSeeder::class);
        $this->call(ReviewTableSeeder::class);
        $this->call(ReportsTableSeeder::class);
        
        // $this->call(UnblockRequestsTableSeeder::class);
                
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
