<?php

use App\UserHourlyRate;

use Illuminate\Database\Seeder;

class UserHourlyRateSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['user_id' => 1, 'rate' => 11.00, 'created_at' => now()],
            ['user_id' => 2, 'rate' => 15.00, 'created_at' => now()],
        ];

        foreach ($items as $item) {
            UserHourlyRate::create($item);
        }
    }
}
