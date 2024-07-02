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
        Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('title',200);
                $table->string('slug',200);
                $table->text('short_description');
                $table->text('description');
                $table->string('is_active',200);
                $table->string('image_url',200);
                $table->unsignedBigInteger('brand_id');
                $table->foreign('brand_id')->references('id')->on('brands');
                $table->text('seo_title_facebook')->nullable()->after('seo_description');
                $table->text('seo_description_facebook')->nullable();
                $table->text('seo_title_twitter')->nullable();
                $table->text('seo_description_twitter')->nullable();
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
        Schema::dropIfExists('products');
    }
};
