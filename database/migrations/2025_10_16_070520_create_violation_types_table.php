<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViolationTypesTable extends Migration
{
    public function up()
    {
        Schema::create('violation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'Late Entry', 'Fighting'
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('violation_types');
    }
}

