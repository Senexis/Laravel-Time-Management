<?php

use App\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['id' => 1, 'name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('admin'), 'travel_expenses' => 0.11],
            ['id' => 2, 'name' => 'User', 'email' => 'user@example.com', 'password' => Hash::make('user'), 'travel_expenses' => 0.19],
        ];

        foreach ($items as $item) {
            User::create($item);
        }
    }
}
