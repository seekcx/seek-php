<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column_topic', function (Blueprint $table) {
            $table->integer('column_id')->index()->comment('专栏ID');
            $table->integer('topic_id')->index()->comment('话题ID');
            $table->timestamps();

            $table->unique(['column_id', 'topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('column_topic');
    }
}
