<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class AddDetailsToRoomAllocationsTable extends Migration
    {
        public function up(): void
        {
            Schema::table('room_allocations', function (Blueprint $table) {
                $table->unsignedBigInteger('hostel_id')->nullable()->after('student_id');
                $table->unsignedBigInteger('building_id')->nullable()->after('hostel_id');
                $table->integer('floor')->nullable()->after('building_id');

                // Optional: Add foreign keys
                $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('set null');
                $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');
            });
        }

        public function down(): void
        {
            Schema::table('room_allocations', function (Blueprint $table) {
                $table->dropForeign(['hostel_id']);
                $table->dropForeign(['building_id']);
                $table->dropColumn(['hostel_id', 'building_id', 'floor']);
            });
        }
    }
