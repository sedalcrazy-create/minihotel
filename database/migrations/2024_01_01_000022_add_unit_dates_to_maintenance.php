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
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->date('start_date')->nullable()->after('description');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['unit_id', 'start_date', 'end_date']);
        });
    }
};
