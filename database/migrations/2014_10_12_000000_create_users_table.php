<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->string('cif_nif')->nullable();
            $table->string('picture')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->enum('status', ['ACTIVE', 'PENDANT', 'BLOCKED'])->default('ACTIVE');
            $table->string('block_reason')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('phone_visible')->default(false);
            $table->string('country_code_whatsapp')->nullable();
            $table->string('phone_number_whatsapp')->nullable();
            $table->boolean('phone_visible_whatsapp')->default(false);
            $table->boolean('verified')->default(false);
            $table->string('profession')->nullable();
            $table->string('province')->nullable();
            $table->boolean('hide_address')->default(true);
            $table->string('city')->nullable();                             
            $table->string('zip_code')->nullable();
            $table->string('address')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->boolean('find_job')->nullable();
            $table->boolean('online')->default(true);
            $table->string('company_name')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
