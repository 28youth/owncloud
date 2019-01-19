<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abilities', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name', 50)->comment('权限名');
            $table->unsignedMediumInteger('parent_id')->default(0)->comment('上级ID');
            $table->unsignedSmallInteger('is_lock')->default(0)->comment('是否锁定');
            $table->unsignedTinyInteger('sort')->default(0)->comment('排序');
            $table->timestamps();

            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abilities');
    }
}
