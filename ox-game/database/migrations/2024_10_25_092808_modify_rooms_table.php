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
        Schema::table('rooms', function (Blueprint $table) {
            // 既存の外部キー制約を削除
            $table->dropForeign(['player_1']);
            $table->foreign('player_1')->references('id')->on('players')->onDelete('cascade');

            // 既存の外部キー制約を削除
            $table->dropForeign(['player_2']);
            $table->foreign('player_2')->references('id')->on('players')->onDelete('cascade');

            // 既存の外部キー制約を削除
            $table->dropForeign(['game_id']);
            $table->foreignId('game_id')->nullable()->unique()->change();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            //
        });
    }
};
