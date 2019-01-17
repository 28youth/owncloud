<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffHasRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_has_roles', function (Blueprint $table) {
            $table->integer('staff_sn')->unsigned()->comment('员工编号');
            $table->mediumInteger('role_id')->unsigned()->comment('角色ID');

            $table->primary(['role_id', 'staff_sn']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_has_roles');
    }
}
