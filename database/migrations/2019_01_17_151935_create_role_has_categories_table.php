<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleHasCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_has_categories', function (Blueprint $table) {
            $table->mediumInteger('role_id')->unsigned()->comment('角色ID');
            $table->mediumInteger('category_id')->unsigned()->comment('分类ID');
            $table->unsignedTinyInteger('file_upload')->comment('文件上传权限');
            $table->unsignedTinyInteger('file_delete')->comment('文件删除权限');
            $table->unsignedTinyInteger('file_edit')->comment('文件编辑权限');
            $table->unsignedTinyInteger('file_expired')->comment('文件过期时间权限');
            $table->unsignedTinyInteger('file_edit_tag')->comment('标签编辑权限');
            $table->unsignedTinyInteger('file_download')->comment('文件下载权限（&在线预览）');

            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['role_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_has_categories');
    }
}
