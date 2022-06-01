<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->bigInteger('facultyId')->unsigned()->comment('รหัสคณะ')->after('id');
            $table->foreign('facultyId')->references('id')->on('faculty')->onDelete('cascade');
            $table->bigInteger('departmentId')->unsigned()->comment('รหัสสาขาวิชา')->after('facultyId');
            $table->foreign('departmentId')->references('id')->on('department')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
