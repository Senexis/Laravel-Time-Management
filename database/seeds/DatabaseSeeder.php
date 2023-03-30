<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProjectSeed::class);
        $this->call(WorkTypeSeed::class);
        $this->call(UserSeed::class);
        $this->call(UserHourlyRateSeed::class);
        $this->call(UserLocationSeed::class);
        $this->call(TimeEntrySeed::class);

        $this->call(PermissionSeed::class);
    }
}
