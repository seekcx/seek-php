<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic', function (Blueprint $table) {
            $table->increments('id');
            $table->char('name', 32)->unique()->comment('名称');
            $table->integer('founder_id')->index()->unsigned()->comment('创建用户');
            $table->string('icon')->nullable()->default('')->comment('图标');
            $table->string('summary', 500)->comment('描述');
            $table->integer('user_count')->default(0)->unsigned()->comment('总用户');
            $table->integer('article_count')->default(0)->unsigned()->comment('总文章');
            $table->integer('column_count')->default(0)->unsigned()->comment('总专栏');
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
        Schema::dropIfExists('topic');
    }
}
