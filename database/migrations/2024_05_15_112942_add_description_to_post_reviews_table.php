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
        Schema::table('post_reviews', function (Blueprint $table) {
            //
            $table->string('description')->nullable();
            $table->float('average_rating')->nullable();
            $table->float('total_score')->nullable();
            $table->char('is_cornerstone')->default('N');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_reviews', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('average_rating');
            $table->dropColumn('total_score');
            $table->dropColumn('is_cornerstone');

        });
    }
};
