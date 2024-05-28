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
        Schema::table('users', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->text('facebook_url')->nullable()->after('description');
            $table->text('instagram_url')->nullable()->after('facebook_url');
            $table->text('linkedin_url')->nullable()->after('instagram_url');
            $table->text('google_url')->nullable()->after('linkedin_url');
            $table->text('twitter_url')->nullable()->after('google_url');
            $table->text('youtube_url')->nullable()->after('twitter_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('facebook_url');
            $table->dropColumn('instagram_url');
            $table->dropColumn('linkedin_url');
            $table->dropColumn('google_url');
            $table->dropColumn('twitter_url');
            $table->dropColumn('youtube_url');

        });
    }
};
