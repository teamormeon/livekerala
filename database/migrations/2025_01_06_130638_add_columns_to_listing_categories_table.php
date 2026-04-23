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
        Schema::table('listing_categories', function (Blueprint $table) {
            $table->string('mobile_app_image')->after('icon')->nullable();
            $table->string('image_driver')->after('mobile_app_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_categories', function (Blueprint $table) {
            $table->dropColumn('mobile_app_image','image_driver');
        });
    }
};
