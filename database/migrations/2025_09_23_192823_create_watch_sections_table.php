<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('watch_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');   // who uploaded it
            $table->string('title', 100);
            $table->string('video_link');            // e.g. YouTube/Vimeo/MP4 URL
            $table->string('image')->nullable();     // thumbnail image path
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_sections');
    }
};
