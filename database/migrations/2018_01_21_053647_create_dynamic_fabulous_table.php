<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynamicFabulousTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_fabulous', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index()->unsigned()->comment('用户 ID');
            $table->integer('flow_id')->index()->unsigned()->comment('动态流 ID');
            $table->char('type')->index()->comment('类型');
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
        Schema::dropIfExists('dynamic_fabulous');
    }
}
