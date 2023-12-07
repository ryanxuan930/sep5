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
            $table->char('event_code', 8);
        });
        Schema::create($header.'teams', function (Blueprint $table) {
            $table->id('team_id');
            $table->bigInteger('org_id');
            $table->bigInteger('dept_id')->default(0);
            $table->string('team_name', 32)->nullable();
            $table->json('member_list')->nullable();
            $table->boolean('verified')->default(1);
        });
        Schema::create($header.'temps', function (Blueprint $table) {
            $table->id('temp_id');
            $table->string('temp_key', 128);
            $table->json('temp_data');
        });
    }

    /*
    For Tournament
    */
    public static function generalSchema(string $header) {
        Schema::create($header.'individuals', function(Blueprint $table){
            $table->id('ind_id');
            $table->bigInteger('u_id');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->smallInteger('ini_group')->default(1);
            $table->smallInteger('ini_position')->default(1);
            $table->json('options')->nullable();
        });
        Schema::create($header.'groups', function(Blueprint $table){
            $table->id('grp_id');
            $table->integer('team_id')->default(0);
            $table->integer('division_id')->default(0);
            $table->char('event_code', 8);
            $table->smallInteger('ini_group')->default(1);
            $table->smallInteger('ini_position')->default(1);
            $table->json('options')->nullable();
        });
        Schema::create($header.'params', function(Blueprint $table){
            $table->id('param_id');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->string('locked_ip', 15)->nullable();// 鎖定一台ip與帳號進行輸入資料
            $table->bigInteger('locked_admin')->default(0);
            $table->json('data')->nullable(); 
        });
        Schema::create($header.'schedules', function(Blueprint $table){
            $table->id('schedule_id');
            $table->datetime('timestamp');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->tinyInteger('round');
            $table->smallInteger('session'); // 場次
            $table->tinyInteger('status')->default(0);
            $table->text('options')->nullable();
        });
    }

    /*
    For Athletics and Swimming
    */
    public static function laneSchema(string $header) {
        // athletics or swimming
        Schema::create($header.'individuals', function(Blueprint $table){
            $table->id('ind_id');
            $table->bigInteger('u_id');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->string('ref_result',12)->default(0);
            // heat, lane, result, rank, remark
            for($i=1; $i<=4; $i++){
                $table->tinyInteger('r'.$i.'_heat')->default(0);
                $table->tinyInteger('r'.$i.'_lane')->default(0);
                $table->string('r'.$i.'_result',12)->default(0);
                $table->smallInteger('r'.$i.'_ranking')->default(0);
                $table->text('r'.$i.'_options')->default('{}');
            }
            $table->json('options')->nullable();
        });
        Schema::create($header.'groups', function(Blueprint $table){
            $table->id('grp_id');
            $table->integer('team_id');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->string('ref_result',12)->default(0);
            // heat, lane, result, rank, remark
            for($i=1; $i<=4; $i++){
                $table->tinyInteger('r'.$i.'_heat')->default(0);
                $table->tinyInteger('r'.$i.'_lane')->default(0);
                $table->string('r'.$i.'_result',12)->default(0);
                $table->smallInteger('r'.$i.'_ranking')->default(0);
                $table->text('r'.$i.'_options')->default('{}');
            }
            $table->json('options')->nullable();
        });
        Schema::create($header.'params', function(Blueprint $table){
            $table->id('param_id');
            $table->integer('division_id');
            $table->char('event_code', 8);
            // [ [Preliminary, Round 1], [Round 1, Repechage], Semi-final, Final]
            for($i=1; $i<=4; $i++){
                // qualifiying method
                $table->boolean('r'.$i)->default(0); // 
                $table->tinyInteger('r'.$i.'_aq')->default(0);
                $table->tinyInteger('r'.$i.'_sq')->default(0);
                $table->tinyInteger('r'.$i.'_split')->default(1);
            }
        });
        Schema::create($header.'schedules', function(Blueprint $table){
            $table->id('schedule_id');
            $table->datetime('timestamp');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->tinyInteger('round')->default(1);
            $table->tinyInteger('status')->default(0);
            $table->text('options')->nullable();
        });

        Schema::create($header.'lanes', function(Blueprint $table){
            $table->integer('lane_id');
            $table->tinyInteger('straight')->default(0);
            $table->tinyInteger('round')->default(0);
        });
        Schema::create($header.'points', function(Blueprint $table){
            $table->increments(' point_id');
            $table->char('org_id',5);
            $table->bigInteger('dept_id')->default(0);
            $table->integer('points')->default(0);
            $table->text('options')->nullable();
        });
        Schema::create($header.'bibs', function (Blueprint $table) {
            $table->id('bib_id');
            $table->bigInteger('u_id');
            $table->string('bib',16)->nullable();
        });
    }

    /*
    For Road Race
    */
    public static function roadSchema(string $header){
        Schema::create($header.'individuals', function(Blueprint $table){
            $table->id('ind_id');
            $table->bigInteger('u_id');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->string('bib', 16);
            $table->string('ref_result',12)->default(0);
            $table->string('result',12)->default(0);
            $table->smallInteger('ranking')->default(0);
            $table->json('options')->nullable('{}');
        });
        Schema::create($header.'params', function(Blueprint $table){
            $table->id('param_id');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->tinyInteger('qualified')->default(0); // 各組取數
            $table->json('options')->nullable();
        });
        Schema::create($header.'schedules', function(Blueprint $table){
            $table->id('schedule_id');
            $table->datetime('timestamp');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->tinyInteger('status')->default(0);
            $table->json('options')->nullable();
        });
        Schema::create($header.'rates', function(Blueprint $table){
            $table->id('rate_id');
            $table->char('org_id',5);
            $table->bigInteger('dept_id')->default(0);
            $table->integer('registered')->default(0);
            $table->integer('finished')->default(0);
            $table->json('options')->nullable();
        });
        Schema::create($header.'bibs', function (Blueprint $table) {
            $table->id('bib_id');
            $table->bigInteger('u_id');
            $table->string('bib',16)->nullable();
        });
    }

    /*
    For Others
    */
    public static function otherSchema(string $header){
        Schema::create($header.'individuals', function(Blueprint $table){
            $table->id('ind_id');
            $table->bigInteger('u_id');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->json('options')->nullable();
        });
        Schema::create($header.'groups', function(Blueprint $table){
            $table->id('grp_id');
            $table->integer('team_id')->default(0);
            $table->integer('division_id')->default(0);
            $table->char('event_code', 8);
            $table->json('options')->nullable();
        });
        Schema::create($header.'schedules', function(Blueprint $table){
            $table->id('schedule_id');
            $table->datetime('timestamp');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->tinyInteger('status')->default(0);
            $table->json('options')->nullable();
        });
        Schema::create($header.'params', function(Blueprint $table){
            $table->id('param_id');
            $table->integer('division_id');
            $table->char('event_code', 8);
            $table->json('options')->nullable();
        });
    }

    /*
    For specified sport
    */
    public static function customSchema($header, $sportsCode) {
    }

    public static function make(int $gameId, string $sportCode, string $module = 'ge') {
        $header = $sportCode.'_'.$gameId.'_';
        GameMaker::commonSchema($header);
        if ($module == 'ge') {
            GameMaker::generalSchema($header);
        }
        if ($module == 'ln') {
            GameMaker::laneSchema($header);
        }
        if ($module == 'rd') {
            GameMaker::roadSchema($header);
        }
        if ($module == 'ot') {
            GameMaker::otherSchema($header);
        }
        GameMaker::customSchema($header, $sportCode);
    }

    public static function reverse(int $gameId, string $sportCode, string $module = 'ge') {
        $header = $sportCode.'_'.$gameId.'_';
        Schema::dropIfExists($header.'dates');
        Schema::dropIfExists($header.'divisions');
        Schema::dropIfExists($header.'selected_events');
        Schema::dropIfExists($header.'teams');
        Schema::dropIfExists($header.'temps');
        Schema::dropIfExists($header.'individuals');
        Schema::dropIfExists($header.'groups');
        Schema::dropIfExists($header.'params');
        Schema::dropIfExists($header.'schedules');
        Schema::dropIfExists($header.'lanes');
        Schema::dropIfExists($header.'points');
        Schema::dropIfExists($header.'rates');
    }
}

?>