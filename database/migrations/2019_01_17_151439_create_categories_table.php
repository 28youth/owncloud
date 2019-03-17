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
            $table->char('full_name', 100)->comment('分类全名');
            $table->char('symbol', 10)->comment('分类编号/代号');
            $table->smallInteger('policy_id')->comment('分类文件策略');
            $table->unsignedMediumInteger('parent_id')->default(0)->comment('上级分类ID');
            $table->unsignedTinyInteger('is_lock')->default(0)->comment('锁定 0:否 1:是');
            $table->unsignedTinyInteger('is_expired')->default(0)->comment('是否含过期文件 0:否 1:是');
            $table->unsignedTinyInteger('allow_edit')->default(1)->comment('是否允许修改文件 0:否 1:是');
            $table->char('operate', 100)->default('')->comment('文件操作');
            $table->char('abilities', 100)->default('')->comment('操作权限');
            $table->char('dirrule', 100)->default('')->comment('目录规则');
            $table->char('numberrule', 100)->default('')->comment('编号规则');
            $table->char('filetype', 100)->default('')->comment('文件格式');
            $table->bigInteger('max_size')->default(0)->comment('单文件最大大小');

            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'symbol']);
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
