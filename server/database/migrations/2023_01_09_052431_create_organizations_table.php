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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id('org_id');
            // info
            $table->tinyInteger('org_type')->default(0); // 0 general 1 univ 2 sr high school 3 jr high school 4 primary school 5 others
            $table->string('org_name_full_ch', 32);
            $table->string('org_name_ch', 8);
            $table->string('org_name_full_en', 128)->nullable();
            $table->string('org_name_en', 8)->nullable();
            $table->text('logo_image')->nullable();
            $table->json('union_id')->nullable(); // 隸屬聯盟標籤
            // location
            $table->char('country_code', 2)->default('TW');
            $table->string('zipcode', 6)->nullable();
            $table->char('city_code', 2)->nullable();
            $table->string('address', 128)->nullable();
            // contact
            $table->string('telephone', 16)->nullable();
            $table->string('telephone_ex', 8)->nullable();
            $table->string('fax', 16)->nullable();
            $table->string('contact_name', 64);
            $table->string('contact_email', 128);
            $table->string('contact_phone', 16);
            $table->string('homepage', 256)->nullable();
            $table->json('links')->nullable();
            // head
            $table->string('chairman_ch', 64)->nullable();
            $table->string('chairman_en', 128)->nullable();
            $table->text('chairman_image')->nullable();
            $table->string('leader_ch', 64)->nullable();
            $table->string('leader_en', 128)->nullable();
            $table->text('leader_image')->nullable();
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
        Schema::dropIfExists('organizations');
    }
};
