<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFacultyIdFromParcel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcel', function (Blueprint $table) {
            $table->dropForeign('parcel_facultyid_foreign');
            $table->dropColumn('facultyId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcel', function (Blueprint $table) {
            $table->bigInteger('facultyId');
        });
    }
}
