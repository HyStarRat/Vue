<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersTableTypeEnumValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER table `users` MODIFY COLUMN 
                `type` enum('super', 'admin', 'moderator',  
                'country_manager', 'team_leader')  
                NOT NULL DEFAULT 'moderator'"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(
            "ALTER table `users` MODIFY COLUMN 
                `type` enum('super', 'admin', 'moderator')  
                NOT NULL DEFAULT 'moderator'"
        );
    }
}
