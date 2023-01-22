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
        Schema::create('admin_unions', function (Blueprint $table) {
            $table->id('admin_union_id');
            $table->string('admin_union_name_ch', 32);
            $table->string('admin_union_name_en', 64);
            $table->json('remarks')->nullable();
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
        Schema::dropIfExists('admin_unions');
    }
};
