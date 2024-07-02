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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('title',100);
            $table->string('slug',100);
            $table->float('price');
            $table->float('discount_price')->nullable();
            $table->string('discount_price_in',20)->nullable();
            $table->date('discount_price_start_date')->nullable();
            $table->date('discount_price_end_date')->nullable();
            $table->float('qty')->default('0');
            $table->string('image_url','200')->nullable();
            $table->char('is_default','1')->default('N');
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
        Schema::dropIfExists('product_variants');
    }
};
