<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HostelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hostels')->insert([
            [
                'name' => 'Alpha Boys Hostel',
                'code' => 'HSTL001',
                'gender' => 'male',
                'building' => 'Block A',
                'total_capacity' => 120,
                'warden' => 'Mr. Raj Sharma',
                'contact' => '9876543210',
                'email' => 'alpha@hostel.com',
                'address' => 'North Campus, Sector 5',
                'facilities' => json_encode(['wifi', 'laundry', 'mess', 'gym']),
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Beta Girls Hostel',
                'code' => 'HSTL002',
                'gender' => 'female',
                'building' => 'Block B',
                'total_capacity' => 100,
                'warden' => 'Mrs. Anjali Kapoor',
                'contact' => '9876501234',
                'email' => 'beta@hostel.com',
                'address' => 'South Campus, Main Lane',
                'facilities' => json_encode(['wifi', 'security', 'tv room']),
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
