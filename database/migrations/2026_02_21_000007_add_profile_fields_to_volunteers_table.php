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
        Schema::table('volunteers', function (Blueprint $table) {
            if (! Schema::hasColumn('volunteers', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('short_bio');
            }
            if (! Schema::hasColumn('volunteers', 'show_photo_on_website')) {
                $table->boolean('show_photo_on_website')->default(true)->after('profile_photo_path');
            }
            if (! Schema::hasColumn('volunteers', 'show_on_website')) {
                $table->boolean('show_on_website')->default(true)->after('show_photo_on_website');
            }
            if (! Schema::hasColumn('volunteers', 'degree_details')) {
                $table->text('degree_details')->nullable()->after('show_on_website');
            }
            if (! Schema::hasColumn('volunteers', 'degree_document_path')) {
                $table->string('degree_document_path')->nullable()->after('degree_details');
            }
            if (! Schema::hasColumn('volunteers', 'certificates_document_path')) {
                $table->string('certificates_document_path')->nullable()->after('degree_document_path');
            }
            if (! Schema::hasColumn('volunteers', 'awards')) {
                $table->text('awards')->nullable()->after('certificates_document_path');
            }
            if (! Schema::hasColumn('volunteers', 'teaching_profile_notes')) {
                $table->text('teaching_profile_notes')->nullable()->after('awards');
            }
        });

        Schema::table('volunteers', function (Blueprint $table) {
            $table->index('show_on_website');
            $table->index('show_photo_on_website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            if (Schema::hasColumn('volunteers', 'teaching_profile_notes')) {
                $table->dropColumn('teaching_profile_notes');
            }
            if (Schema::hasColumn('volunteers', 'awards')) {
                $table->dropColumn('awards');
            }
            if (Schema::hasColumn('volunteers', 'certificates_document_path')) {
                $table->dropColumn('certificates_document_path');
            }
            if (Schema::hasColumn('volunteers', 'degree_document_path')) {
                $table->dropColumn('degree_document_path');
            }
            if (Schema::hasColumn('volunteers', 'degree_details')) {
                $table->dropColumn('degree_details');
            }
            if (Schema::hasColumn('volunteers', 'show_on_website')) {
                $table->dropColumn('show_on_website');
            }
            if (Schema::hasColumn('volunteers', 'show_photo_on_website')) {
                $table->dropColumn('show_photo_on_website');
            }
            if (Schema::hasColumn('volunteers', 'profile_photo_path')) {
                $table->dropColumn('profile_photo_path');
            }
        });
    }
};

