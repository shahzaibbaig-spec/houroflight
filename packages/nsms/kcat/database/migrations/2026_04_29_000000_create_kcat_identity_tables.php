<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kcat_access', function (Blueprint $table): void {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->string('role', 20);
        });
        Schema::create('kcat_school_classes', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('section')->default('');
            $table->timestamps(); $table->unique(['name', 'section']);
        });
        Schema::create('kcat_students', function (Blueprint $table): void {
            $table->id(); $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('student_id')->unique(); $table->string('name'); $table->string('father_name')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('kcat_school_classes')->nullOnDelete();
            $table->string('status', 20)->default('active'); $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('kcat_students');
        Schema::dropIfExists('kcat_school_classes');
        Schema::dropIfExists('kcat_access');
    }
};
