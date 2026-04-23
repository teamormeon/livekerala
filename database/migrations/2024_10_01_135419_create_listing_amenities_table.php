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
        Schema::create('listing_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->index()->nullable();
            $table->foreignId('purchase_package_id')->index()->nullable();
            $table->foreignId('amenity_id')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_amenities');
    }
};
