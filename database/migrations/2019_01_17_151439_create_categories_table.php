<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->char('name', 20)->comment('分类名称');
            $table->char('symbol', 10)->comment('分类编号（参与文件名生成）');
            $table->smallInteger('service_id')->comment('服务器配置ID');
            $table->unsignedMediumInteger('parent_id')->default(0)->comment('上级分类ID');
            $table->unsignedSmallInteger('is_lock')->default(0)->comment('锁定 0:否 1:是');
            $table->char('namerule', 100)->nullable()->comment('文件编号规则');
            $table->char('operate', 100)->nullable()->comment('文件操作');
            $table->char('abilities', 100)->nullable()->comment('操作权限');
            $table->char('filetype', 100)->nullable()->comment('文件格式');
            $table->char('description', 100)->nullable()->comment('分类备注');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
