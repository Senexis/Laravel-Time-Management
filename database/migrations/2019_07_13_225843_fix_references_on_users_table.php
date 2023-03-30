<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixReferencesOnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_project', 'last_work_type', 'last_location']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('last_project')->after('locale')->unsigned()->nullable();
            $table->foreign('last_project')->references('id')->on('projects');

            $table->integer('last_work_type')->after('last_project')->unsigned()->nullable();
            $table->foreign('last_work_type')->references('id')->on('work_types');

            $table->integer('last_location')->after('last_work_type')->unsigned()->nullable();
            $table->foreign('last_location')->references('id')->on('user_locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
