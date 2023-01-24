<?php

namespace App\Mods;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class GameMaker {

    private static function commonSchema(string $header) {
        // game fundamental configs
        Schema::create($header.'dates', function (Blueprint $table) {
            $table->id('date_id');
            $table->date('date');
            $table->string('day_ch',16)->nullable();
            $table->string('day_en',32)->nullable();
        });

        Schema::create($header.'divisions', function (Blueprint $table) {
            $table->id('division_id');
            $table->tinyInteger('sex')->default(0);
            $table->string('division_ch',16);
            $table->string('division_en',32)->nullable();
        });

        Schema::create($header.'selected_events', function (Blueprint $table) {
            $table->id('selected_event_id');
            $table->bigInteger('event_id');
        });
        Schema::create($header.'teams', function (Blueprint $table) {
            $table->id('team_id');
            $table->bigInteger('org_id');
            $table->bigInteger('dept_id')->default(0);
            $table->string('team_name', 32)->nullable();
            $table->json('member_list')->nullable();
            $table->boolean('verified')->default(1);
        });
    }
    public static function make(int $gameId, string $sportCode, string $module = 'ge') {
        $header = $sportCode.'_'.$gameId.'_';
        GameMaker::commonSchema($header);
        if ($module == 'ge') {

        }
        if ($module == 'ln') {
            
        }
        if ($module == 'rd') {
            
        }
    }
    public static function reverse(int $gameId, GameModule $module, string $sportCode) {
        $header = $sportCode.'_'.$gameId.'_';
        Schema::dropIfExists($header.'dates');
        Schema::dropIfExists($header.'divisions');
        Schema::dropIfExists($header.'selected_events');
        Schema::dropIfExists($header.'teams');
    }
}

?>