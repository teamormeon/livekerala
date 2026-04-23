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
        Schema::create('purchase_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->foreignId('package_id')->index();
            $table->foreignId('trx_id')->nullable();
            $table->foreignId('deposit_id')->nullable();
            $table->float('price', 12, 2)->nullable();
            $table->tinyInteger('is_renew')->default(0);
            $table->tinyInteger('is_image')->default(0);
            $table->tinyInteger('is_video')->default(0);
            $table->tinyInteger('is_amenities')->default(0);
            $table->tinyInteger('is_product')->default(0);
            $table->tinyInteger('is_create_from')->default(0);
            $table->tinyInteger('is_business_hour')->default(0);
            $table->string('no_of_listing')->nullable();
            $table->integer('no_of_img_per_listing')->nullable();
            $table->integer('no_of_categories_per_listing')->default(1);
            $table->integer('no_of_amenities_per_listing')->nullable();
            $table->integer('no_of_product')->nullable();
            $table->integer('no_of_img_per_product')->nullable();
            $table->tinyInteger('seo')->default(1);
            $table->tinyInteger('is_whatsapp')->default(0);
            $table->tinyInteger('is_messenger')->default(0);
            $table->tinyInteger('status')->default(0)->comment('0=>pending, (fund=2) 1=>approved, 2=>cancel (fund=3)');
            $table->string('type')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->timestamp('last_reminder_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_packages');
    }
};
