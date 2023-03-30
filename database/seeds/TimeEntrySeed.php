<?php

use App\TimeEntry;

use Illuminate\Database\Seeder;

class TimeEntrySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['id' => 1, 'user_id' => 2, 'project_id' => 1, 'work_type_id' => 2, 'location_id' => 5, 'notes' => 'Added the ability to add notes.', 'start_time' => date('Y-m-d') . ' 08:00:00', 'end_time' => date('Y-m-d') . ' 11:30:00'],
            ['id' => 2, 'user_id' => 2, 'project_id' => 1, 'work_type_id' => 3, 'location_id' => 6, 'notes' => 'Helped Jane Doe fix her issues.', 'start_time' => date('Y-m-d') . ' 13:10:00', 'end_time' => date('Y-m-d') . ' 13:50:00']
        ];

        foreach ($items as $item) {
            TimeEntry::create($item);
        }
    }
}
