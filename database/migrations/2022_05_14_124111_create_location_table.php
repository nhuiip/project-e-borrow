<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location', function (Blueprint $table) {
            $table->id()->comment('รหัสสถานที่จัดเก็บ');
            $table->bigInteger('facultyId')->unsigned()->comment('รหัสคณะ')->nullable();
            $table->foreign('facultyId')->references('id')->on('faculty')->onDelete('cascade');
            $table->bigInteger('departmentId')->unsigned()->comment('รหัสสาขาวิชา')->nullable();
            $table->foreign('departmentId')->references('id')->on('department')->onDelete('cascade');
            $table->string('name')->comment('ชื่อสถานที่จัดเก็บ');
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
        Schema::dropIfExists('location');
    }
}
