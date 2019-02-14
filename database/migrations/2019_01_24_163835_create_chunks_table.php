<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChunksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chunks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('上传者');
            $table->mediumInteger('cate_id')->comment('分类ID');
            $table->char('file_hash', 32)->comment('文件hash');
            $table->char('chunk_key', 16)->comment('当前分片key');
            $table->char('chunk_name', 16)->comment('分片文件名');
            $table->integer('chunk_id')->comment('当前分片ID');
            $table->integer('chunk_sum')->comment('分片总数');
            $table->dateTime('up_time')->comment('分片上传时间');

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
        Schema::dropIfExists('chunks');
    }
}
