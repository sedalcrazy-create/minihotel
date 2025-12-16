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
        Schema::create('personnel', function (Blueprint $table) {
            $table->id();
            $table->string('employment_code', 50)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->smallInteger('birth_year');
            $table->tinyInteger('birth_month');
            $table->tinyInteger('birth_day');
            $table->string('national_code', 10)->unique();
            $table->string('father_name', 100)->nullable();
            $table->string('relation', 50)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('service_location_code', 50)->nullable();
            $table->string('service_location')->nullable();
            $table->string('department_code', 50)->nullable();
            $table->string('department')->nullable();
            $table->string('employment_status', 100);
            $table->string('main_or_branch', 50)->nullable();
            $table->string('funkefalat')->nullable();
            $table->string('partner_employment_status', 100)->nullable();
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'employment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel');
    }
};
