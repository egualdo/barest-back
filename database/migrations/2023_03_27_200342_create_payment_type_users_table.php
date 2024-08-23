<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTypeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_type_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('payment_type_id');
            $table->integer('field1');
            $table->integer('field2');
            $table->integer('field3'); 
            $table->integer('field4');
            $table->integer('status');
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
        Schema::dropIfExists('payment_type_users');
    }
}
