<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('machine_sn');
            $table->unsignedBigInteger('project_id');
            $table->string('pile_name')->nullable();
            // $table->double('pile_x')->nullable();
            // $table->double('pile_y')->nullable();
            $table->json('content')->nullable();
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
        Schema::dropIfExists('work_records');
    }
}
