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
        Schema::table('posts', function (Blueprint $table) {
            
            $table->text('seo_title_facebook')->nullable()->after('seo_description');
            $table->text('seo_description_facebook')->nullable();
            $table->text('seo_title_instagram')->nullable();
            $table->text('seo_description_instagram')->nullable();
            $table->text('seo_title_twitter')->nullable();
            $table->text('seo_description_twitter')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('seo_title_facebook');
            $table->dropColumn('seo_description_facebook');
            $table->dropColumn('seo_title_instagram');
            $table->dropColumn('seo_description_instagram');
            $table->dropColumn('seo_title_twitter');
            $table->dropColumn('seo_description_twitter');
        });
    }
};
