<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiometricAttendencesTable extends Migration
{
    public function up()
    {
        Schema::create('biometric_attendences', function (Blueprint $table) {
            $table->id();

            $table->string('student_name')->nullable(); // Employee Name (nvarchar/char)
            
            $table->timestamp('log_date_time')->nullable(); // Log Date Time (datetime)

            $table->date('log_date')->nullable(); // Log Date (date)
            $table->time('log_time')->nullable(); // Log Time (time)

            $table->timestamp('download_date_time')->nullable(); // Download Date Time (datetime)

            $table->string('device_serial_no', 50)->nullable(); // Device Serial No (nvarchar/char)
            $table->string('device_no', 50)->nullable(); // Device No (char/int as string)
            $table->string('device_name')->nullable(); // Device Name (nvarchar)

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('biometric_attendences');
    }
}
