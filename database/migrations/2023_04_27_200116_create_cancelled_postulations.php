<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCancelledPostulations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancelled_postulations', function (Blueprint $table) {
            $table->id();
            $table->integer('cancelled_by_user_id')->default(0);
            $table->integer('postulation_id')->default(0);
            $table->foreignId('motive_id');
            $table->string('other_motive')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('cancelled_postulations');
    }
}
