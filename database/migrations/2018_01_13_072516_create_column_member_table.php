<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column_member', function (Blueprint $table) {
            $table->integer('user_id')->index()->comment('用户ID');
            $table->integer('column_id')->index()->comment('专栏ID');
            $table->integer('role')->index()->comment('角色');
            $table->timestamps();

            $table->unique(['column_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('column_member');
    }
}
