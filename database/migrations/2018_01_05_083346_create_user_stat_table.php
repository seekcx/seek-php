<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserStatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_stat', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->primary()->comment('用户ID');
            $table->integer('followers')->default(0)->comment('粉丝数');
            $table->integer('following')->default(0)->comment('关注数');
            $table->integer('topic')->default(0)->comment('话题数');
            $table->integer('column')->default(0)->comment('专栏数');
            $table->integer('group')->default(0)->comment('小组数');
            $table->integer('score')->default(0)->comment('社区隐藏评分');
            $table->timestamp('register_at')->nullable()->comment('注册时间');
            $table->ipAddress('register_ip')->default('')->comment('注册IP');
            $table->ipAddress('last_login_ip')->default('')->comment('最近一次登录IP');
            $table->timestamp('last_login_at')->nullable()->comment('最近一次登录时间');
            $table->ipAddress('last_active_ip')->default('')->comment('最近一次活跃IP');
            $table->timestamp('last_active_at')->nullable()->comment('最近一次活跃时间');
            $table->integer('login_count')->default(1)->comment('登录次数');
            $table->integer('active_count')->default(1)->comment('活跃次数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_stat');
    }
}
