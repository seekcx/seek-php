<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column', function (Blueprint $table) {
            $table->increments('id');
            $table->char('name', 64)->unique()->comment('名称');
            $table->char('link', 32)->unique()->comment('链接');
            $table->integer('founder_id')->index()->comment('创办人');
            $table->integer('owner_id')->index()->comment('所有者');
            $table->string('icon')->default('')->comment('图标');
            $table->integer('member_count')->default(1)->comment('成员数');
            $table->string('summary', 500)->default('')->comment('描述');
            $table->ipAddress('created_ip')->default('')->comment('创建IP');
            $table->ipAddress('updated_ip')->default('')->comment('更新IP');
            $table->timestamps();
            $table->tinyInteger('state')->default(0)->comment('状态：-1、删除，0、待审，1、正常');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('column');
    }
}
