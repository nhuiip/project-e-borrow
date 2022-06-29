<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history', function ($table) {
            $table->bigInteger('typeId')->unsigned()->comment('รหัสประเภท')->after('parcelId');
            $table->foreign('typeId')->references('id')->on('history_type')->onDelete('cascade');
            $table->bigInteger('statusId')->unsigned()->comment('รหัสสถานะ')->after('typeId');
            $table->foreign('statusId')->references('id')->on('history_status')->onDelete('cascade');
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
