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
        Schema::create('product_queries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->nullable()->comment('receiver');
            $table->foreignId('client_id')->index()->nullable()->comment('sender');
            $table->foreignId('listing_id')->index()->nullable();
            $table->foreignId('product_id')->index()->nullable();
            $table->longText('message')->nullable();
            $table->string('customer_enquiry')->default(0);
            $table->string('my_enquiry')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_queries');
    }
};
