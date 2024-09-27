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
        Schema::table('blind_camera_tests', function (Blueprint $table) {
            $table->char('is_highlighted',1)->default('N');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blind_camera_tests', function (Blueprint $table) {
            $table->dropColumn('is_highlighted');

        });
    }
};
