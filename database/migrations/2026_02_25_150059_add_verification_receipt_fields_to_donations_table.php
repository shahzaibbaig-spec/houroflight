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
        Schema::table('donations', function (Blueprint $table) {
            if (! Schema::hasColumn('donations', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('donations', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('verified_at')
                    ->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('donations', 'receipt_number')) {
                $table->string('receipt_number')->nullable()->unique()->after('verified_by');
            }

            if (! Schema::hasColumn('donations', 'receipt_sent_at')) {
                $table->timestamp('receipt_sent_at')->nullable()->after('receipt_number');
            }

            if (! Schema::hasColumn('donations', 'receipt_pdf_path')) {
                $table->string('receipt_pdf_path')->nullable()->after('receipt_sent_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'receipt_pdf_path')) {
                $table->dropColumn('receipt_pdf_path');
            }

            if (Schema::hasColumn('donations', 'receipt_sent_at')) {
                $table->dropColumn('receipt_sent_at');
            }

            if (Schema::hasColumn('donations', 'receipt_number')) {
                $table->dropUnique(['receipt_number']);
                $table->dropColumn('receipt_number');
            }

            if (Schema::hasColumn('donations', 'verified_by')) {
                $table->dropConstrainedForeignId('verified_by');
            }

            if (Schema::hasColumn('donations', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
        });
    }
};
