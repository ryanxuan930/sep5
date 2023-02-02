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
        Schema::create('school_teams', function (Blueprint $table) {
            $table->id('school_team');
            $table->bigInteger('org_id');
            $table->string('team_name_ch', 16);
            $table->string('team_name_en', 128);
            $table->integer('sport_id');
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
        Schema::dropIfExists('school_teams');
    }
};
