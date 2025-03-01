<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        Project::create(['name' => 'Project Alpha', 'status' => 'active']);
        Project::create(['name' => 'Project Beta', 'status' => 'inactive']);
    }
}
