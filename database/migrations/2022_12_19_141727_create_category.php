<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_level_2', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('group_id');
            $table->boolean('featured')->default(false);
            $table->boolean('only_market')->default(false);
            $table->boolean('active')->default(true);
            $table->string('icon')->nullable();
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
        Schema::dropIfExists('category_level_2');
    }
}
