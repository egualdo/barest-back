<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $picture = [
                    "https://play-lh.googleusercontent.com/fk1PBadTRlGq67UFQ_3Wx0GGgz929AUNpmyKa8vGaoT1UovXKssiPpurOMQo9bhc_Eo=w240-h480-rw",
                    "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpysVTBqZ7GtNL9H5jnF7AQOBCdiOMentPMw&usqp=CAU",
                    "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSa69_HGc_i3MXKCPZzCfAjBZC4bXJsn0rS0Ufe6H-ctZz5FbIVaPkd1jCPTpKwPruIT3Q&usqp=CAU"
                ];
        
        $isCompanyRole = fake()->boolean();
        $isProfessionalRole = fake()->boolean(65);
        $city = DB::table('cities')->where('state_id', 1158)->inRandomOrder()->first();
        
        return [
            'username' => $this->faker->userName(),            
            'company_name' => $isCompanyRole ? $this->faker->companyPrefix().' '.$this->faker->bs() : null,
            'cif_nif' => $isCompanyRole || $isProfessionalRole ? $this->faker->vat() : null,
            'profession' => !$isCompanyRole || $isProfessionalRole ? $this->faker->jobTitle() : null,
            'email' => $this->faker->unique()->safeEmail(),
            'picture' => $picture[array_rand($picture,1)],
            'province' => 'Madrid',
            'city' => $city->name,
            'zip_code' => $this->faker->postcode(),
            'email_verified_at' => now(),
            'password' => '123456789', // password,
            'country_code' => '+34',
            'phone_number' => $this->faker->tollFreeNumber(),
            'description' => $this->faker->paragraph(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
