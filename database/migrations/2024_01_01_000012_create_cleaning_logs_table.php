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
        Schema::create('cleaning_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('bed_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('cleaned_at')->useCurrent();
            $table->enum('type', ['daily', 'weekly', 'deep'])->default('daily');
            $table->foreignId('cleaned_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['cleaned_at', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleaning_logs');
    }
};
