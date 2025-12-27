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
        Schema::create('mess', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            // hotel_id foreign key
            $table->unsignedBigInteger('hostel_id');
            $table->foreign('hostel_id')
                  ->references('id')
                  ->on('hostels')
                  ->onDelete('cascade');
            
            $table->text('menu_document_upload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mess');
    }
};
