<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department', function (Blueprint $table) {
            $table->id()->comment('รหัสสาขาวิชา');
            $table->bigInteger('facultyId')->unsigned()->comment('รหัสคณะ');
            $table->foreign('facultyId')->references('id')->on('faculty')->onDelete('cascade');
            $table->string('name')->comment('ชื่อสาขาวิชา');
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
        Schema::dropIfExists('department');
    }
}
