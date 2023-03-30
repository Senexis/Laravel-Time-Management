<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_locations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('name');
            $table->double('distance', 6, 1);

            $table->timestamps();
            $table->softDeletes();
            $table->index(['deleted_at']);
        });

        Schema::table('users', function ($table) {
            $table->double('travel_expenses', 6, 2)->after('hourly_rate');
        });

        Schema::table('time_entries', function ($table) {
            $table->integer('location_id')->unsigned()->after('work_type_id');
            $table->foreign('location_id')->references('id')->on('user_locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_locations');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('travel_expenses');
        });

        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
    }
}
