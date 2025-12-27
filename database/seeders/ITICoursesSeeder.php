<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ITICoursesSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            ['name' => 'Electrician', 'description' => 'Electrical wiring, maintenance, and installation', 'duration_months' => 24],
            ['name' => 'Fitter', 'description' => 'Fitting parts and assembling machinery', 'duration_months' => 24],
            ['name' => 'Welder', 'description' => 'Welding and metal joining techniques', 'duration_months' => 24],
            ['name' => 'Mechanic Diesel', 'description' => 'Diesel engine maintenance and repair', 'duration_months' => 24],
            ['name' => 'Computer Operator', 'description' => 'Basic computer operation and office automation', 'duration_months' => 12],
            ['name' => 'Carpenter', 'description' => 'Woodwork and carpentry skills', 'duration_months' => 24],
            ['name' => 'Plumber', 'description' => 'Installation and repair of plumbing systems', 'duration_months' => 24],
            ['name' => 'Wireman', 'description' => 'Electrical wiring and installations', 'duration_months' => 24],
        ];

        DB::table('courses')->insert($courses);
    }
}
