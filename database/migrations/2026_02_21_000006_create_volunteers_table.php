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
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('expertise_subjects');
            $table->string('grade_levels');
            $table->string('availability');
            $table->string('lesson_format');
            $table->unsignedInteger('years_experience')->nullable();
            $table->text('short_bio')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index('status');
            $table->index('lesson_format');
            $table->index('availability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};

