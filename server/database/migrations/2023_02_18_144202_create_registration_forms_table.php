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
        Schema::create('registration_forms', function (Blueprint $table) {
            $table->id('reg_form_id');
            $table->bigInteger('org_id');
            $table->bigInteger('dept_id')->default(0);
            $table->bigInteger('u_id')->default(0);
            $table->bigInteger('game_id');
            $table->tinyInteger('status')->default(0);
            $table->string('remarks', 256)->nullable();
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
        Schema::dropIfExists('registration_forms');
    }
};
