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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('player_1',32)->unique()->nullable();
            $table->foreign('player_1')->references('id')->on('players');
            $table->string('player_2',32)->unique()->nullable();
            $table->foreign('player_2')->references('id')->on('players');
            $table->foreignId('game_id')->constrained()->unique()->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
