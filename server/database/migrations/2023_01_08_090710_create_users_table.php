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
        Schema::create('users', function (Blueprint $table) {
            $table->id('u_id');
            // account
            $table->string('account', 128);
            $table->string('password', 256);
            if (env('USE_MONKEYID')) {
                $table->char('monkey_user_id', 64)->nullable();
                $table->tinyInteger('user_identity')->default(0);
            }
            // name
            $table->string('first_name_ch', 64);
            $table->string('last_name_ch', 16);
            $table->string('first_name_en', 128)->nullable();
            $table->string('last_name_en', 128)->nullable();
            // organization
            $table->char('org_id', 5);
            $table->boolean('is_student')->default(0);
            $table->string('student_id', 16)->nullable();
            $table->bigInteger('dept_id')->default(0);
            $table->tinyInteger('grade')->default(0);
            // personal info
            $table->char('unified_id', 10)->nullable();
            $table->date('birthday')->nullable();
            $table->char('nationality', 2)->default('TW');
            $table->boolean('is_indigenous')->default(0);
            $table->integer('indigenous_tribe_id')->default(0);
            $table->boolean('is_sport_gifited')->default(0);
            $table->integer('gifited_sport_id')->default(0);
            $table->boolean('is_school_team')->default(0);
            $table->json('school_team_id_list')->nullable();
            // physical info
            $table->tinyInteger('sex')->default(0); // others 1 male 2 female
            $table->smallInteger('height')->default(0);
            $table->smallInteger('weight')->default(0);
            $table->string('blood_type', 4)->nullable();
            // contact info
            $table->string('cellphone', 16)->nullable();
            $table->string('telephone', 16)->nullable();
            $table->char('household_cty_code', 2)->nullable();
            $table->string('address', 128)->nullable();
            $table->string('emergercy_contact', 64)->nullable();
            $table->string('emergercy_phone', 16)->nullable();
            // others
            $table->json('options')->nullable();
            $table->text('avatar')->nullable();
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
        Schema::dropIfExists('user');
    }
};
