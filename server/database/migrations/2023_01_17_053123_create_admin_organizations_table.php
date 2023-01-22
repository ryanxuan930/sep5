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
        Schema::create('admin_organizations', function (Blueprint $table) {
            $table->id('admin_org_id');
            $table->string('admin_org_name_ch', 32);
            $table->string('admin_org_name_en', 64);
            $table->json('admin_union_list')->nullable();
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
        Schema::dropIfExists('admin_organizations');
    }
};
