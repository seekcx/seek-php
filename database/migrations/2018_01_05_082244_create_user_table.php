<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->char('name', 32)->unique()->comment('名字');
            $table->char('mobile', 20)->unique()->comment('手机号');
            $table->char('email', 64)->unique()->nullable()->comment('邮箱');
            $table->char('password', 64)->comment('密码');
            $table->char('avatar', 255)->default('')->comment('头像');
            $table->tinyInteger('gender')->default(0)->comment('性别（0、未设置，1、男，2、女）');
            $table->date('birthday')->default('1990-01-01')->comment('生日');
            $table->char('summary', 128)->default('')->comment('简介');
            $table->string('introduction')->default('')->comment('个人信息');
            $table->integer('region_id')->unsigned()->default(100000)->comment('所在地区');
            $table->timestamps();
            $table->tinyInteger('state')->default(1)->index()->comment('状态');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
