<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLnkUsersInstagramProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lnk_users_instagram_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('instagram_profile_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('lnk_users_instagram_profiles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('instagram_profile_id')->references('id')->on('instagram_profiles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lnk_users_instagram_profiles', function (Blueprint $table) {
            $table->dropForeign($table->getTable() . '_' . 'user_id' . '_foreign');
            $table->dropForeign($table->getTable() . '_' . 'instagram_profile_id' . '_foreign');
        });

        Schema::dropIfExists('lnk_users_instagram_profiles');
    }
}
