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
        Schema::table('medias', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('image', 255)->change();
            $table->text('caption')->change();
            $table->text('description')->change();
            $table->text('alt_text')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medias', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->string('caption', 100)->change();
            $table->string('description', 100)->change();
            $table->string('alt_text')->change();

        });
    }
};
