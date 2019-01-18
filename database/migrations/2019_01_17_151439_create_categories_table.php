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
            $table->unsignedMediumInteger('parent_id')->default(0)->comment('上级分类ID');
            $table->unsignedSmallInteger('is_system')->default(0)->comment('系统 0:否 1:是');
            $table->char('config_number', 100)->nullable()->comment('配置编号规则');
            $table->char('config_operate', 100)->nullable()->comment('配置操作类型');
            $table->char('config_ability', 100)->nullable()->comment('配置操作权限');
            $table->char('config_format', 100)->nullable()->comment('配置文件格式');
            $table->char('config_path', 100)->nullable()->comment('配置存储路径');
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
