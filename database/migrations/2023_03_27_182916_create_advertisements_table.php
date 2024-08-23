<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisementsTable extends Migration
{
    
    public function up()
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->integer('user_id');
            $table->integer('plan_id');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('advertisements');
    }
}
