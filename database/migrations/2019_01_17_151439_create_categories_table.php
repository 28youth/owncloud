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
            $table->unsignedInteger('parent_id')->comment('上级分类ID');
            $table->unsignedSmallInteger('is_system')->default(0)->comment('系统分类 0:否 1:是');
            $table->string('config_number')->comment('配置编号规则');
            $table->string('config_operate')->comment('配置操作类型');
            $table->string('config_ability')->comment('配置操作权限');
            $table->string('config_format')->comment('配置文件格式');
            $table->string('config_path')->comment('配置存储路径');
            $table->string('description')->nullable()->comment('分类备注');

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
