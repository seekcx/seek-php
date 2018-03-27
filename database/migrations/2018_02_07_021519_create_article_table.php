<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->index()->unsigned()->comment('作者ID');
            $table->integer('column_id')->index()->unsigned()->comment('专栏ID');
            $table->string('title', 72)->comment('标题');
            $table->json('image')->comment('图片');
            $table->string('summary', 512)->comment('摘要');
            $table->text('content')->comment('内容');
            $table->integer('edit_count')->default(0)->comment('编辑次数');
            $table->ipAddress('created_ip')->default('')->comment('创建IP');
            $table->ipAddress('updated_ip')->default('')->comment('更新IP');
            $table->timestamps();
            $table->tinyInteger('state')->default(0)->comment('状态');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article');
    }
}
