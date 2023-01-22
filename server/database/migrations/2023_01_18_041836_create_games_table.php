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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('game_name_ch', 64);
            $table->string('game_name_en', 256)->nullable();
            $table->string('game_name_jp', 128)->nullable();
            $table->mediumText('game_info')->nullable();
            $table->json('host_list'); // host org-dept object
            $table->bigInteger('create_dept_id'); // create by dept id
            $table->date('event_start')->useCurrent();
            $table->boolean('selected')->default(0); // 0 open 1 selected for participants
            $table->json('selected_list')->nullable(); // valid when selected is true; participants org-dept object
            $table->boolean('use_reg')->default(1);
            $table->text('reg_url')->nullable();
            $table->boolean('use_manage')->default(1);
            $table->text('manage_url')->nullable();
            $table->boolean('use_site')->default(1);
            $table->text('site_url')->nullable();
            $table->json('tags')->nullable(); // tags from game_tags
            $table->timestamps()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
};
