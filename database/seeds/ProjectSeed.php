<?php

use App\Project;

use Illuminate\Database\Seeder;

class ProjectSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['name' => 'Untitled Project 1'],
            ['name' => 'Untitled Project 2'],
            ['name' => 'Freelance'],
        ];

        foreach ($items as $item) {
            Project::create($item);
        }
    }
}
