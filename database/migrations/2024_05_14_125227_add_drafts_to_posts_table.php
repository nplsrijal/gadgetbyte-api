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
            $table->char('status',1)->default('P'); // D,R,
            $table->timestamp('post_on')->nullable();
            $table->char('is_cornerstone')->default('N');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            //
            $table->dropColumn('status');
            $table->dropColumn('post_on');
            $table->dropColumn('is_cornerstone');


        });
    }
};
