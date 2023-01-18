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
        Schema::create('configs', function (Blueprint $table) {
            $table->id('config_id');
            $table->boolean('reg_switch')->default(0); // 報名系統開關
            $table->text('reg_content')->default(''); // 報名系統公告欄
            $table->boolean('root_page')->default(1); // 顯示root主頁面
            $table->boolean('multilingual')->default(0); // 多語言
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
        Schema::dropIfExists('configs');
    }
};
