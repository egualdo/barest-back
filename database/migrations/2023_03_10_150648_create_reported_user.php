<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportedUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_user_id');
            $table->foreignId('reported_user_id');
            $table->foreignId('motive_id')->nullable();
            $table->string('other_motive')->nullable();
            $table->enum('status',['ACCEPTED','PENDANT','REJECTED'])->default('ACCEPTED');
            // $table->string('reportable_type');
            $table->morphs('reportable');
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
        Schema::dropIfExists('reports');
    }
}
