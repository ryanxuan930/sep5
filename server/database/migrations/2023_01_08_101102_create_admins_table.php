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
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('account', 128);
            $table->string('password', 256);
            $table->string('name', 64);
            $table->tinyInteger('permission')->default(0);
            $table->bigInteger('admin_org_id')->default(0);
            $table->bigInteger('admin_dept_id')->default(0);
            $table->json('options')->nullable();
            $table->ipAddress('last_ip')->default('0.0.0.0');
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
        Schema::dropIfExists('admins');
    }
};
