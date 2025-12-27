<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('disciplinary_violations', function (Blueprint $table) {
            $table->decimal('fine_amount', 8, 2)->nullable()->after('status');
            $table->string('fine_reason')->nullable()->after('fine_amount');
            $table->date('fine_issued_at')->nullable()->after('fine_reason');
        });
    }

    public function down() {
        Schema::table('disciplinary_violations', function (Blueprint $table) {
            $table->dropColumn(['fine_amount', 'fine_reason', 'fine_issued_at']);
        });
    }
};
