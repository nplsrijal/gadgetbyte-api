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
        Schema::create('post_with_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id');
                $table->foreign('post_id')->references('id')->on('posts');
                $table->unsignedBigInteger('category_id');
                $table->foreign('category_id')->references('id')->on('post_categories');
                $table->userinfos();
                $table->archivedInfos();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_with_categories');
    }
};
