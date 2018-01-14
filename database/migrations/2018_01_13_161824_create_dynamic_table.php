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
            $table->string('context')->comment('上下文');
            $table->integer('shareable_id')->index()->unsigned()->comment('分享ID');
            $table->char('shareable_type', 32)->index()->comment('分享类型');
            $table->ipAddress('created_ip')->default('')->comment('创建IP');
            $table->ipAddress('updated_ip')->default('')->comment('更新IP');
            $table->timestamps();
            $table->tinyInteger('state')->default(1)->comment('0、已隐藏，1、正常');

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
