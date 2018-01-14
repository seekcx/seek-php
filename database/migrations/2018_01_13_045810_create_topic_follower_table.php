<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicFollowerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_follower', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('topic_id')->index()->unsigned()->comment('话题ID');
            $table->integer('user_id')->index()->unsigned()->comment('用户ID');
            $table->timestamps();

            $table->unique(['topic_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topic_follower');
    }
}
