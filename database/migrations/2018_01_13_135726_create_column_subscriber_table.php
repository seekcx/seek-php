<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnSubscriberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column_subscriber', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('column_id')->index()->unsigned()->comment('话题ID');
            $table->integer('user_id')->index()->unsigned()->comment('用户ID');
            $table->timestamps();

            $table->unique(['column_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('column_subscriber');
    }
}
