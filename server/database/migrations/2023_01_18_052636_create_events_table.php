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
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->bigInteger('sport_id');
            $table->char('event_code', 8);
            $table->string('event_ch', 32);
            $table->string('event_en', 128);
            $table->string('event_jp', 64)->nullable();
            $table->string('event_abbr', 16);
            $table->char('unit', 1)->default('0'); // 0 NA T time D distance
            $table->boolean('display')->default(0);
            $table->boolean('built_in')->default(0);
            $table->boolean('multiple')->default(0); // multiple players
            $table->boolean('combined')->default(0); // is combined event
            $table->json('combined_list')->nullable(); // combined event list
            $table->integer('player_num')->default(0);
            $table->bigInteger('created_by_dept')->default(0);
            $table->json('remarks')->nullable();
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
        Schema::dropIfExists('events');
    }
};
