<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\MessBill;
use Carbon\Carbon;

class GenerateMessBills extends Command
{
    protected $signature = 'mess:generate-bills {--month=}';
    protected $description = 'Generate monthly mess bills for all students';

    public function handle()
    {
        $month = $this->option('month') 
            ? Carbon::parse($this->option('month'))->startOfMonth()
            : now()->startOfMonth();

        $daysInMonth = $month->daysInMonth;
        $ratePerDay = 128;

        $students = Student::all();

        foreach ($students as $student) {
            MessBill::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'month' => $month,
                ],
                [
                    'days' => $daysInMonth,
                    'amount' => $daysInMonth * $ratePerDay,
                    'status' => 'unpaid',
                ]
            );
        }

        $this->info("Mess bills generated for {$month->format('F Y')}.");
    }


    
}