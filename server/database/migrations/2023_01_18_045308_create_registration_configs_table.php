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
        Schema::create('registration_configs', function (Blueprint $table) {
            $table->id('reg_config_id');
            $table->bigInteger('game_id');
            $table->boolean('reg_switch')->default(0);
            $table->json('deadline_list')->nullable(); // {start, end, title_ch, title_en, upload, options}
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
        Schema::dropIfExists('registration_configs');
    }
};
