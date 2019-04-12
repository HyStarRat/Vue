<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryIdToWebsiteUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_users', function (Blueprint $table) {
            $table->integer('country_id')->unsigned()->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website_users', function (Blueprint $table) {
            $table->drop('country_id');
        });
    }
}
