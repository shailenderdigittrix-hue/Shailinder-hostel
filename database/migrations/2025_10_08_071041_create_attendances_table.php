<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // link to student
            $table->text('enrollment_id'); // link to student
            $table->date('attendance_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['Present', 'Absent', 'Leave'])->default('Present');
            $table->text('remarks')->nullable();

            $table->timestamps();

            // foreign key (optional, if students table exists)
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
