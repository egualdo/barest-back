<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use App\Models\PostType;
use App\Models\Category;
use App\Models\ConditionProduct;
use App\Models\ContractType;
use App\Models\Experience;
use App\Models\GroupCategory;
use App\Models\Modality;
use App\Models\PaymentModality;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Post::class;

    public function definition()
    {
        $users = User::role([ 'user', 'company' ])->get();
        $types = PostType::whereNotIn('id', [ 4, 5 ])->get();     
        
        $categories_lvl_1 = GroupCategory::where('post_type_id', $types->random()->id)->get();
        
        $groupCategory = $categories_lvl_1->random();
        
        $category = !$groupCategory->categoryLevel2->isEmpty()
                        ? $groupCategory->categoryLevel2->random()
                        : null;
        
        $categories_lvl_3 = $category && !$category->categoryLevel3->isEmpty()
                                ? $category->categoryLevel3
                                : null;
        
        $experiences = Experience::get();
        $paymentModalities = PaymentModality::get();
        $productConditions = ConditionProduct::get();
        
        $randomLocation = DB::table('random_locations')->get()->random();
        $modalities = Modality::get();

        $user = $users->random()->load('roles');

        // Role_ids => Type_post_ids
        $publicationTypesByRole = [
            1 => $types->whereIn('id', [ 2, 3 ]),
            2 => $types->whereIn('id', [ 1, 2, 3, 4, 5 ])
        ];

        // Publication_type_id => payment_modalities_id
        $paymentModalityByPubType = [
            1 => $paymentModalities->whereIn('id', [ 1, 5 ]),
            2 => null,
            3 => $paymentModalities->whereBetween('id', [ 1, 4 ])
        ];

        // Publication_type_id => modality_id
        $modalityByPublicationType = [
            1 => $modalities,
            2 => null,
            3 => $modalities
        ];

        $picture = [
                    "https://coctelia.com/wp-content/uploads/2020/06/tienda-cocteleria-1024x768.jpg",
                    "https://watermark.lovepik.com/photo/20211208/large/lovepik-hotel-toiletries-close-up-picture_501631440.jpg",
                    "https://tytenlinea.com/wp-content/uploads/2019/11/comercializacion-de-materiales-electricos1.jpg"
                ];
        
        return [
            'title' => $this->faker->text(15),
            'user_id' => $user->id,
            'picture' => $picture[array_rand($picture,1)],
            'description' => $this->faker->text(200),
            'type_post_id' => $publicationTypesByRole[$user->roles->first()->id]->random()->id,            
            'payment_modality_id' => function(Array $attributes) use ($paymentModalityByPubType) {
                return !$paymentModalityByPubType[$attributes['type_post_id']]
                            ? 0
                            : $paymentModalityByPubType[$attributes['type_post_id']]->random()->id;
            },
            'amount' => function(Array $attributes) use ($paymentModalityByPubType) {
                return !$paymentModalityByPubType[$attributes['type_post_id']]
                            ? $this->faker->randomFloat(2, 10, 10000)
                            : $this->faker->randomNumber(5);
            },
            'amount_to' => function(Array $attributes) use ($paymentModalityByPubType) {
                return !$paymentModalityByPubType[$attributes['type_post_id']]
                            ? null
                            : $attributes['amount'] + $this->faker->randomNumber(3);
            },
            'city' => $this->faker->city(),
            'category_1_id'=> $groupCategory->id,
            'category_2_id'=> $category ? $category->id : null,
            'category_3_id'=> $categories_lvl_3 ? $categories_lvl_3->random()->id : null,
            'condition_product_id' => function(Array $attributes) use ($productConditions) {
                return $attributes['type_post_id'] === 2
                            ? $productConditions->random()->id
                            : null;
            },
            'experience_id' => function() use ($user) {
                return $user->getRole()->id === 2
                            ? $this->faker->randomNumber(2)
                            : null;
            },
            'years_experience' => function() use ($user) {
                return $user->getRole()->id === 1
                            ? $this->faker->randomNumber(2)
                            : null;
            },
            'status' => 'ACTIVE',            
            'lat' => $randomLocation->lat,
            'long' => $randomLocation->long,
            'modality_id' => function(Array $attributes) use ($modalityByPublicationType) {
                return !$modalityByPublicationType[$attributes['type_post_id']]
                            ? null
                            : $modalityByPublicationType[$attributes['type_post_id']]->random()->id;
            }
        ];
    }
}
