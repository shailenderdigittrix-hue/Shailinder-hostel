<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessFoodItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
		Schema::create('mess_food_items', function (Blueprint $table) {
		    $table->id();
		    $table->string('name');
            $table->string('category')->comment('veg, non-veg, vegan'); // allowed categories
		    $table->text('description')->nullable();
		    $table->integer('calories')->nullable();
		    $table->text('image')->nullable();
		    $table->decimal('price', 8, 2)->nullable();
		    $table->enum('status', ['Active', 'Inactive'])->default('Active');
		    $table->timestamps();	
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mess_food_items');
    }
}
