<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_number', 32)->comment('文件编号');
            $table->integer('user_id')->comment('操作人');
            $table->text('changes')->comment('变动明细');
            $table->date('operate_at')->comment('执行日期');
            $table->char('operate_type', 20)->comment('操作类型');
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
        Schema::dropIfExists('file_logs');
    }
}
