<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_stock', function (Blueprint $table) {
            $table->id()->comment("รหัสจัดการคลังพัสดุ");
            $table->bigInteger('parcelId')->unsigned()->comment('รหัสพัสดุ');
            $table->foreign('parcelId')->references('id')->on('parcel')->onDelete('cascade');
            $table->integer('stock')->comment('จำนวนพัสดุ');
            $table->integer('stock_type')->comment('1: เติมของ, 2: เอาของออก');
            $table->bigInteger('created_userId')->comment('คนเพิ่มข้อมูล')->nullable();
            $table->bigInteger('updated_userId')->comment('คนแก้ไขข้อมูลล่าสุด')->nullable();
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
        Schema::dropIfExists('parcel_stock');
    }
}
