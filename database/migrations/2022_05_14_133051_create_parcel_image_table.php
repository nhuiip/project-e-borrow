<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_image', function (Blueprint $table) {
            $table->id()->comment('รหัสรูปภาพพัสดุ');
            $table->bigInteger('parcelId')->unsigned()->comment('รหัสพัสดุ');
            $table->foreign('parcelId')->references('id')->on('parcel')->onDelete('cascade');
            $table->string('name')->comment('ชื่อรูปภาพพัสดุ');
            $table->bigInteger('created_userId')->comment('คนเพิ่มรูปภาพพัสดุ')->nullable();
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
        Schema::dropIfExists('parcel_image');
    }
}
