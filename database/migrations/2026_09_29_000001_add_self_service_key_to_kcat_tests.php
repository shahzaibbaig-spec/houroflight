<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kcat_tests', function (Blueprint $table): void {
            $table->string('self_service_key')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('kcat_tests', function (Blueprint $table): void {
            $table->dropUnique(['self_service_key']);
            $table->dropColumn('self_service_key');
        });
    }
};
