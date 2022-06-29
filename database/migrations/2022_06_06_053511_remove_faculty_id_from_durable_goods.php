<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFacultyIdFromDurableGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('durable_goods', function (Blueprint $table) {
            $table->dropForeign('durable_goods_facultyid_foreign');
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
        Schema::table('durable_goods', function (Blueprint $table) {
            $table->bigInteger('facultyId');
        });
    }
}
