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
        Schema::create('department', function (Blueprint $table) {
            $table->id('dept_id');
            $table->char('related_org_id');
            $table->string('dept_name_ch', 32);
            $table->string('dept_name_en', 128)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('has_grade')->default(0);
            $table->tinyInteger('grade')->default(0);
            $table->json('options')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department');
    }
};
