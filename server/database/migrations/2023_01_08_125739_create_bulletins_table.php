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
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id('bulletin_id');
            $table->date('post_date')->useCurrent();
            $table->tinyInteger('category')->default(0);
            $table->boolean('pinned')->default(0);
            $table->timestamps()->useCurrent();
            $table->integer('post_by')->default(0); // admin id
            $table->integer('clicks')->default(0); // click number
            $table->integer('related_game')->default(0); // game_id
            $table->integer('related_admin_dept')->default(0);
            $table->boolean('multilingual')->default(0);
            $table->string('title_ch');
            $table->string('title_en')->nullable();
            $table->longText('content_ch')->nullable();
            $table->longText('content_en')->nullable();
            $table->json('links')->nullable();
            $table->boolean('release')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bulletins');
    }
};
