<?php

use App\UserLocation;

use Illuminate\Database\Seeder;

class UserLocationSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['user_id' => 1, 'name' => 'Home - Office', 'distance' => 13.4],
            ['user_id' => 1, 'name' => 'Home - Home', 'distance' => 0],
            ['user_id' => 1, 'name' => 'Home - External 1', 'distance' => 41.3],
            ['user_id' => 1, 'name' => 'Office - External 1', 'distance' => 27.9],
            ['user_id' => 2, 'name' => 'Home - Office', 'distance' => 13.4],
            ['user_id' => 2, 'name' => 'Home - Home', 'distance' => 0],
            ['user_id' => 2, 'name' => 'Home - External 1', 'distance' => 41.3],
            ['user_id' => 2, 'name' => 'Office - External 1', 'distance' => 27.9],
        ];

        foreach ($items as $item) {
            UserLocation::create($item);
        }
    }
}
