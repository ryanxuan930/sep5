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
        Schema::create('sport_lists', function (Blueprint $table) {
            $table->id('sport_id');
            $table->string('sport_name_ch', 32);
            $table->string('sport_name_en', 64);
            $table->char('sport_code', 4);
            $table->smallInteger('event_id_count')->default(0);
            $table->char('module', 2);
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
        Schema::dropIfExists('sport_lists');
    }
};
