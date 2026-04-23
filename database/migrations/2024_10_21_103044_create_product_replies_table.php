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
        Schema::create('product_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->nullable()->comment('sender');
            $table->foreignId('client_id')->index()->nullable()->comment('receiver');
            $table->foreignId('product_query_id')->index()->nullable();
            $table->longText('reply')->nullable();
            $table->string('file')->nullable();
            $table->string('driver')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=> unseen, 1=> seen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_replies');
    }
};
