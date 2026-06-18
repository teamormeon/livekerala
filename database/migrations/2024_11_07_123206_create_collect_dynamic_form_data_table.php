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
        Schema::create('collect_dynamic_form_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->foreignId('dynamic_forms_id')->index();
            $table->foreignId('listing_id')->index();
            $table->string('form_name')->nullable();
            $table->text('input_form')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collect_dynamic_form_data');
    }
};
