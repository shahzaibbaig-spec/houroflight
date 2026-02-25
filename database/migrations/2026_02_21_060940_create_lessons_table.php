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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('volunteer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('subject');
            $table->unsignedTinyInteger('grade_min');
            $table->unsignedTinyInteger('grade_max');
            $table->string('lesson_type');
            $table->string('delivery_mode');
            $table->string('youtube_url')->nullable();
            $table->string('video_path')->nullable();
            $table->string('document_path')->nullable();
            $table->text('description');
            $table->text('learning_objectives')->nullable();
            $table->string('language')->default('English');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('subject');
            $table->index(['grade_min', 'grade_max']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
