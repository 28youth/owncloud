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
            $table->integer('user_id')->comment('上传者');
            $table->string('hash')->comment('文件hash值');
            $table->string('origin_name')->comment('原文件名');
            $table->string('filename')->comment('文件名');
            $table->string('mime')->comment('文件mime');
            $table->string('size', 50)->comment('文件大小');
            $table->mediumInteger('category_id')->comment('所属分类');
            $table->decimal('width', 8, 2)->nullable()->comment('图片宽');
            $table->decimal('height', 8, 2)->nullable()->comment('图片高');

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
