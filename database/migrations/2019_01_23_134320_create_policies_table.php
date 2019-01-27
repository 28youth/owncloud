<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->char('name', 20)->comment('策略名');
            $table->char('driver', 6)->comment('上传类型 本地:local 远程:remote');
            $table->char('host', 32)->default('')->comment('上传服务器IP');
            $table->smallInteger('port')->default(0)->comment('上传服务器端口');
            $table->char('username', 20)->default('')->comment('上传服务器用户名');
            $table->char('root', 50)->default('')->comment('上传根目录');
            $table->text('privateKey')->nullable()->comment('服务器公钥');
            $table->tinyInteger('timeout')->default(30)->comment('上传超时时间');

            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('policies');
    }
}
