<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->id()->comment('รหัสการทำรายการ');
            $table->bigInteger('durablegoodsId')->unsigned()->comment('รหัสครุภัณฑ์')->nullable();
            $table->foreign('durablegoodsId')->references('id')->on('durable_goods')->onDelete('cascade');
            $table->bigInteger('parcelId')->unsigned()->comment('รหัสพัสดุ')->nullable();
            $table->foreign('parcelId')->references('id')->on('parcel')->onDelete('cascade');
            $table->integer('type')->comment('ประเภทรายการ (1:ครุภัณฑ์, 2:พัสดุ)');
            $table->integer('status')->comment('1:รออนุมัติ, 2:อนุมัติ, 3: คืนแล้ว')->default(1);
            $table->integer('unit')->comment('จำนวน')->default(1);
            $table->bigInteger('approved_userId')->comment('คนอนุมัติ')->nullable();
            $table->timestamp('approved_at')->comment('วันเวลาอนุมัติ')->nullable();
            $table->bigInteger('returned_userId')->comment('คนรับคืน')->nullable();
            $table->timestamp('returned_at')->comment('วันเวลารับคืน')->nullable();
            $table->bigInteger('created_userId')->comment('คนเบิก')->nullable();
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
        Schema::dropIfExists('history');
    }
}
