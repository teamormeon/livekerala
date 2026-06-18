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
        Schema::create('claim_businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_by_id')->index()->nullable();
            $table->foreignId('listing_id')->index()->nullable();
            $table->foreignId('listing_owner_id')->index()->nullable();
            $table->string('uuid')->index()->nullable();
            $table->tinyInteger('is_chat_enable')->default(0);
            $table->tinyInteger('is_chat_start')->default(0);
            $table->tinyInteger('status')->default(0)->comment('0: pending, 1: approved, 3: rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_businesses');
    }
};
