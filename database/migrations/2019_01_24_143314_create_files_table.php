<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('user_id')->comment('上传者');
            $table->char('hash', 32)->comment('文件hash值');
            $table->char('origin_filename', 100)->comment('原文件名');
            $table->char('number', 50)->comment('文件编号');
            $table->char('filename', 100)->comment('文件名');
            $table->char('mime', 150)->comment('文件mime');
            $table->char('size', 50)->comment('文件大小');
            $table->mediumInteger('category_id')->comment('所属分类');

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
        Schema::dropIfExists('files');
    }
}
