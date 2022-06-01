<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDurableGoodsImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('durable_goods_image', function (Blueprint $table) {
            $table->id()->comment('รหัสรูปภาพครุภัณฑ์');
            $table->bigInteger('durablegoodsId')->unsigned()->comment('รหัสครุภัณฑ์');
            $table->foreign('durablegoodsId')->references('id')->on('durable_goods')->onDelete('cascade');
            $table->string('name')->comment('ชื่อรูปภาพครุภัณฑ์');
            $table->bigInteger('created_userId')->comment('คนเพิ่มรูปภาพครุภัณฑ์')->nullable();
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
        Schema::dropIfExists('durable_goods_image');
    }
}
