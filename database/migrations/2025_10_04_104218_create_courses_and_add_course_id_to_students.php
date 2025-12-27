<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesAndAddCourseIdToStudents extends Migration
{
    public function up()
    {
        // Create courses table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Course name, e.g., Electrician, Fitter');
            $table->text('description')->nullable()->comment('Optional course description');
            $table->unsignedInteger('duration_months')->default(6)->comment('Course duration in months');
            $table->timestamps();
        });

        // Add course_id foreign key to students table
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('course_id')->after('phone')->constrained('courses')->cascadeOnDelete();
        });
    }

    public function down()
    {
        // Remove course_id foreign key and column from students
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });

        // Drop courses table
        Schema::dropIfExists('courses');
    }
}
