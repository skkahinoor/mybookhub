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
        if (Schema::hasTable('class_subjects')) {
            Schema::rename('class_subjects', 'filter_class_subject');
        }

        Schema::table('filter_class_subject', function (Blueprint $table) {
            // Drop old foreign key if it exists
            // Since table rename might not have renamed the foreign key, it could still be called the same
            if (Schema::hasColumn('filter_class_subject', 'subcategory_id')) {
                // We'll try to drop it safely. Laravel might not detect it if named specifically.
                try {
                    $table->dropForeign('class_subjects_subcategory_id_foreign');
                } catch (\Exception $e) {
                    // Ignore if it doesn't exist
                }
                $table->dropColumn('subcategory_id');
            }

            // Add section_id and category_id after id
            if (!Schema::hasColumn('filter_class_subject', 'section_id')) {
                $table->unsignedBigInteger('section_id')->after('id');
            }
            if (!Schema::hasColumn('filter_class_subject', 'category_id')) {
                $table->unsignedBigInteger('category_id')->after('section_id');
            }

            if (!Schema::hasColumn('filter_class_subject', 'sub_category_id')) {
                $table->unsignedBigInteger('sub_category_id')->after('category_id');
            }

            // Optional: Re-add foreign keys
            // $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            // $table->foreign('sub_category_id')->references('id')->on('subcategories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('filter_class_subject')) {
            Schema::table('filter_class_subject', function (Blueprint $table) {
                if (Schema::hasColumn('filter_class_subject', 'sub_category_id')) {
                    $table->dropColumn('sub_category_id');
                }
                if (!Schema::hasColumn('filter_class_subject', 'subcategory_id')) {
                    $table->unsignedBigInteger('subcategory_id')->after('id');
                }
                if (Schema::hasColumn('filter_class_subject', 'section_id')) {
                    $table->dropColumn('section_id');
                }
                if (Schema::hasColumn('filter_class_subject', 'category_id')) {
                    $table->dropColumn('category_id');
                }
            });

            Schema::rename('filter_class_subject', 'class_subjects');
        }
    }
};
