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
        Schema::table('personnel', function (Blueprint $table) {
            $table->enum('person_type', ['اصلی', 'غیراصلی'])->default('اصلی')->after('gender');
            $table->string('colleague_status', 50)->default('شاغل')->after('person_type');
            $table->date('last_sync_date')->nullable()->after('colleague_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            $table->dropColumn(['person_type', 'colleague_status', 'last_sync_date']);
        });
    }
};
