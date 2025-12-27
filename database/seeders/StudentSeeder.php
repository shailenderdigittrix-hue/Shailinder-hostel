<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $studentsData = [
            [
                'user' => [
                    'name' => 'Alice Johnson',
                    'email' => 'alice@example.com',
                    'password' => Hash::make('password'),
                ],
                'student' => [
                    'enrollment_no' => 'B10001',
                    'first_name' => 'Alice',
                    'last_name' => 'Johnson',
                    'gender' => 'Female',
                    'date_of_birth' => '2001-03-12',
                    'email' => 'alice.student@example.com',
                    'phone' => '9876543210',
                    'course' => 'Information Technology',
                    'year' => 2,
                    'address' => '101 First Street',
                    'admission_date' => '2023-07-01',
                ]
            ],
            [
                'user' => [
                    'name' => 'Bob Smith',
                    'email' => 'bob@example.com',
                    'password' => Hash::make('password'),
                ],
                'student' => [
                    'enrollment_no' => 'B10002',
                    'first_name' => 'Bob',
                    'last_name' => 'Smith',
                    'gender' => 'Male',
                    'date_of_birth' => '2000-11-20',
                    'email' => 'bob.student@example.com',
                    'phone' => '1234567890',
                    'course' => 'Computer Science',
                    'year' => 3,
                    'address' => '202 Second Street',
                    'admission_date' => '2022-08-15',
                ]
            ],
            [
                'user' => [
                    'name' => 'Charlie Lee',
                    'email' => 'charlie@example.com',
                    'password' => Hash::make('password'),
                ],
                'student' => [
                    'enrollment_no' => 'B10003',
                    'first_name' => 'Charlie',
                    'last_name' => 'Lee',
                    'gender' => 'Other',
                    'date_of_birth' => '2002-05-05',
                    'email' => 'charlie.student@example.com',
                    'phone' => '5551234567',
                    'course' => 'Mechanical Engineering',
                    'year' => 1,
                    'address' => '303 Third Street',
                    'admission_date' => '2024-09-01',
                ]
            ],
            [
                'user' => [
                    'name' => 'Diana Prince',
                    'email' => 'diana@example.com',
                    'password' => Hash::make('password'),
                ],
                'student' => [
                    'enrollment_no' => 'B10004',
                    'first_name' => 'Diana',
                    'last_name' => 'Prince',
                    'gender' => 'Female',
                    'date_of_birth' => '1999-12-31',
                    'email' => 'diana.student@example.com',
                    'phone' => '4449876543',
                    'course' => 'Business Administration',
                    'year' => 4,
                    'address' => '404 Fourth Avenue',
                    'admission_date' => '2021-06-10',
                ]
            ],
        ];


        foreach ($studentsData as $entry) {
            $user = User::create($entry['user']);

            Student::create(array_merge($entry['student'], [
                'user_id' => $user->id,
            ]));
        }
    }
}

