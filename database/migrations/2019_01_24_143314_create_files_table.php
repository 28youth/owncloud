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
            $table->char('origin_name', 100)->comment('原文件名');
            $table->char('number', 50)->comment('文件编号');
            $table->char('filename', 100)->comment('文件名');
            $table->char('mime', 150)->comment('文件mime');
            $table->char('size', 50)->comment('文件大小');
            $table->decimal('width', 8, 2)->nullable()->comment('图片宽');
            $table->decimal('height', 8, 2)->nullable()->comment('图片高');
            $table->mediumInteger('category_id')->comment('所属分类');
            $table->mediumInteger('download_sum')->defaule(0)->comment('下载次数');
            $table->dateTime('expired_at')->nullable()->comment('文件过期时间');

            $table->timestamps();
            $table->unique('number');
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
