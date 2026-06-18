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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_owner_id')->index()->nullable();
            $table->foreignId('listing_id')->index()->nullable();
            $table->string('visitor_ip')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('code')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('os_platform')->nullable();
            $table->text('browser')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
