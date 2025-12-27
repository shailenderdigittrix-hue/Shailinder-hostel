<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Basic Info
            $table->string('name')->unique();                      // e.g., A-Block
            $table->string('code')->unique();                      // e.g., HSTL001
            $table->enum('gender', ['male', 'female', 'co-ed']);   // For gender-specific hostels
            $table->string('building')->nullable();                // Optional: A, B, C block etc.
            $table->integer('total_capacity');                     // Total number of beds
            // Optional: Contact & Management Info
            $table->string('warden')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            // Optional: Facilities JSON (e.g., wifi, laundry, etc.)
            $table->json('facilities')->nullable();
            $table->boolean('is_active')->default(true);           // Mark hostel as active/inactive

            // Audit Info
            $table->unsignedBigInteger('created_by')->nullable();  // Admin/User ID who created
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            // Foreign keys (optional, if you have users table)
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('hostels');
    }

};