<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    public function up()
    {
        Schema::create('plans', function (Blueprint $table){
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->json('benefits')->nullable();
            $table->timestamps();
            $table->boolean('active')->default(true);
            $table->string('color');
            $table->softDeletes();
            // $table->float('price')->default(0);
            // $table->string('duration', 100);
            // $table->integer('max_advertisement')->default(0);
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
