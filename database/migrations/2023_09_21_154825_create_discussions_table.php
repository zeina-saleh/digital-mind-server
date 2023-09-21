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
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idea_id');
            $table->string('title');
            $table->foreign('idea_id')->references('id')->on('ideas')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('discussion_user', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('discussion_id');
        $table->unsignedBigInteger('user_id');

        $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussions');
    }
};
