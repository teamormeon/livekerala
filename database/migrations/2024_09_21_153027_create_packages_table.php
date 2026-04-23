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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->float('price', 12, 2)->nullable();
            $table->tinyInteger('is_multiple_time_purchase')->default(0);
            $table->tinyInteger('is_renew')->default(0);
            $table->integer('expiry_time')->nullable();
            $table->string('expiry_time_type')->nullable();
            $table->boolean('is_image')->default(false);
            $table->boolean('is_video')->default(false);
            $table->boolean('is_amenities')->default(false);
            $table->boolean('is_product')->default(false);
            $table->boolean('is_business_hour')->default(false);
            $table->string('no_of_listing')->nullable();
            $table->integer('no_of_img_per_listing')->nullable();
            $table->integer('no_of_categories_per_listing')->default(1);
            $table->integer('no_of_amenities_per_listing')->nullable();
            $table->integer('no_of_product')->nullable();
            $table->integer('no_of_img_per_product')->nullable();
            $table->tinyInteger('is_create_from')->default(0);
            $table->boolean('seo')->default(1);
            $table->tinyInteger('is_whatsapp')->default(0);
            $table->tinyInteger('is_messenger')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('image')->default(null);
            $table->string('driver')->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
