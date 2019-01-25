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
            $table->char('policy_name', 20)->comment('策略名');
            $table->char('policy_type', 6)->comment('上传类型 本地:local 远程:server');
            $table->char('server_host', 32)->default()->comment('上传服务器IP');
            $table->smallInteger('server_port')->default()->comment('上传服务器端口');
            $table->char('username', 20)->default()->comment('上传服务器用户名');
            $table->char('root_path', 50)->default()->comment('上传根目录');
            $table->text('privat_key')->nullable()->comment('服务器公钥');
            $table->bigInteger('max_size')->default(0)->comment('单文件最大大小');
            $table->char('dirrule')->default()->comment('目录规则');
            $table->char('namerule')->default()->comment('文件名规则');
            $table->char('filetype', 100)->default()->comment('文件格式');
            $table->tinyInteger('timeout')->default(30)->comment('上传超时时间');
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
