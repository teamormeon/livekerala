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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->nullable();
            $table->foreignId('category_id')->index()->nullable();
            $table->foreignId('purchase_package_id')->index()->nullable();
            $table->foreignId('place_id')->index()->nullable();
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->text('youtube_video_id')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('thumbnail_driver')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 =>pending, 1=> approved, 2=> rejected');
            $table->tinyInteger('is_active')->default(1)->comment('0=>deactive, 1=>active');
            $table->longText('rejected_reason')->nullable();
            $table->longText('deactive_reason')->nullable();
            $table->string('fb_app_id')->nullable();
            $table->string('fb_page_id')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->text('replies_text')->nullable();
            $table->longText('body_text')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
