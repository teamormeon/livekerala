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
        Schema::create('dynamic_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->nullable();
            $table->foreignId('package_id')->index()->nullable();
            $table->foreignId('listing_id')->index()->nullable();
            $table->string('name')->nullable();
            $table->string('button_text')->nullable();
            $table->text('input_form')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0: Inactive, 1: Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_forms');
    }
};
