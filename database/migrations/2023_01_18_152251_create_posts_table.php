<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
           $table->id();
           $table->integer('user_id');
           $table->integer('type_post_id');
            $table->string('title');
            $table->string('description');
            $table->string('picture')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('address')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->integer('category_1_id');
            $table->integer('category_2_id')->nullable();
            $table->integer('category_3_id')->nullable();
            $table->integer('priority')->default(0);
            $table->integer('condition_product_id')->nullable();
            $table->integer('modality_id')->nullable();
            $table->integer('payment_modality_id')->nullable();
            $table->double('amount');
            $table->double('amount_to')->nullable();
            $table->integer('experience_id')->nullable();
            $table->unsignedInteger('years_experience')->nullable();
            $table->longText('requeriments')->nullable();
            $table->longText('benefits')->nullable();
            $table->string('more_info')->nullable();
            $table->boolean('custom_budget')->default(false);
            $table->boolean('working_holidays')->default(false);
            $table->double('delivery_price')->default(0);
            $table->double('hour_price')->default(0);
            $table->enum('status',['ACTIVE','PENDANT','BLOCKED','STOPPED'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
