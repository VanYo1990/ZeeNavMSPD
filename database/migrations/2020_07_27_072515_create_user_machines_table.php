<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		//从 Laravel 5.8 开始，默认的自增 ID 的字段类型从原本的 int 改成了 big int，相应的 migration 代码应该使用 bigIncrements() 方法，定义对应自增 ID 的外键时也需要使用 unsignedBigInteger() 方法
        Schema::create('user_machines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->unsignedBigInteger('machine_sn');
            $table->foreign('machine_sn')->references('sn')->on('machines')->onDelete('cascade');
            $table->dateTime('verified_at')->nullable();
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
        Schema::dropIfExists('user_machines');
    }
}
