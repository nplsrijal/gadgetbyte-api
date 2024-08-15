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
        Schema::create('blind_camera_tests', function (Blueprint $table) {
            $table->id();
            $table->string('product_a_title',200);
            $table->string('product_b_title',200);
            $table->string('cover_image',200);
            $table->json('product_a_images');
            $table->json('product_b_images');
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
        Schema::dropIfExists('blind_camera_tests');
    }
};
