<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynamicFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_flow', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dynamic_id')->index()->unsigned()->comment('动态ID');
            $table->integer('author_id')->index()->unsigned()->comment('作者ID');
            $table->tinyInteger('type')->index()->unsigned()->comment('类型');
            $table->string('content', 500)->default('')->comment('内容');
            $table->integer('repost_count')->default(0)->comment('转发次数');
            $table->integer('comment_count')->default(0)->comment('评论次数');
            $table->integer('fabulous_count')->default(0)->comment('点赞次数');
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
        Schema::dropIfExists('dynamic_flow');
    }
}
