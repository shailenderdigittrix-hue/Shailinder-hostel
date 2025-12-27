<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('building_id')->nullable()->after('hostel_id');
            $table->string('floor')->nullable()->after('building_id');

            // Add foreign key constraint
            $table->foreign('building_id')
                ->references('id')
                ->on('buildings')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropColumn(['building_id', 'floor']);
        });
    }
};
