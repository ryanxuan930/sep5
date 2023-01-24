<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_tags', function (Blueprint $table) {
            $table->id('game_tag_id');
            $table->string('game_tag_ch', 32);
            $table->string('game_tag_en', 128)->nullable();
            $table->string('game_tag_jp', 64)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_tags');
    }
};
