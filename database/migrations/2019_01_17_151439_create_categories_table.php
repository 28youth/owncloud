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
            $table->smallInteger('policy_id')->comment('分类文件策略');
            $table->unsignedMediumInteger('parent_id')->default(0)->comment('上级分类ID');
            $table->unsignedSmallInteger('is_lock')->default(0)->comment('锁定 0:否 1:是');
            $table->char('operate', 100)->default('')->comment('文件操作');
            $table->char('abilities', 100)->default('')->comment('操作权限');
            $table->char('dirrule')->default('')->comment('目录规则');
            $table->char('namerule')->default('')->comment('文件名规则');
            $table->char('filetype', 100)->default('')->comment('文件格式');
            $table->bigInteger('max_size')->default(0)->comment('单文件最大大小');
            $table->char('description', 100)->default('')->comment('分类备注');

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
