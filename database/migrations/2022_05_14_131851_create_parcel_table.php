<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel', function (Blueprint $table) {
            $table->id()->comment('รหัสพัสดุ');
            $table->bigInteger('facultyId')->unsigned()->comment('รหัสคณะ');
            $table->foreign('facultyId')->references('id')->on('faculty')->onDelete('cascade');
            $table->bigInteger('departmentId')->unsigned()->comment('รหัสสาขาวิชา');
            $table->foreign('departmentId')->references('id')->on('department')->onDelete('cascade');
            $table->bigInteger('locationId')->unsigned()->comment('รหัสสถานที่จัดเก็บ');
            $table->foreign('locationId')->references('id')->on('location')->onDelete('cascade');
            $table->string('reference')->comment('เลขที่พัสดุ');
            $table->string('name')->comment('ชื่อพัสดุ');
            $table->integer('stock')->comment('จำนวนพัสดุ');
            $table->string('stock_unit')->comment('หน่วยนับพัสดุ');
            $table->integer('status')->comment('1: ปิดไม่ให้เบิก, 2: ของหมด, 3: เบิกได้')->default(2);
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
        Schema::dropIfExists('parcel');
    }
}
