<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynamicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->index()->unsigned()->comment('作者ID');
            $table->char('type', 32)->index()->comment('类型');
            $table->string('content', 500)->default('')->comment('内容');
            $table->integer('shareable_id')->index()->unsigned()->comment('分享ID');
            $table->char('shareable_type', 32)->index()->comment('分享类型');
            $table->ipAddress('created_ip')->default('')->comment('创建IP');
            $table->ipAddress('updated_ip')->default('')->comment('更新IP');
            $table->timestamps();
            $table->tinyInteger('state')->default(1)->comment('-1、已锁定，0、已删除，1、正常');

            $table->unique(['shareable_id', 'shareable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dynamic');
    }
}
