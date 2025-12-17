<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('audio_files', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('bytescale_id')->nullable();
            $table->string('bytescale_path')->nullable();
        });
    }

    public function down()
    {
        Schema::table('audio_files', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'bytescale_id', 'bytescale_path']);
        });
    }
};