<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MessBill;
use Illuminate\Support\Facades\Artisan;


class MessBillController extends Controller
{
    public function generateBills($month) {
        $exitCode = Artisan::call('mess:generate-bills', [
            '--month' => $month,
        ]);

        $output = Artisan::output();

        return response()->json([
            'exit_code' => $exitCode,
            'output' => $output,
            'message' => "Command executed for month: $month"
        ]);
    }



    public function index() {
        $bills = MessBill::with('student')->orderBy('month', 'desc')->paginate(20);
        // dd($bills);
        return view('backend.MessManagement.mess_bills.list', compact('bills'));
    }

    public function markPaid(MessBill $bill) {
        $bill->update(['status' => 'paid']);
        return back()->with('success', 'Bill marked as paid.');
    }

    public function report() {
        $totalRevenue = MessBill::where('status', 'paid')->sum('amount');
        $outstanding = MessBill::where('status', 'unpaid')->sum('amount');
        $studentsWithDue = MessBill::where('status', 'unpaid')->distinct('student_id')->count();

        return view('mess_bills.report', compact('totalRevenue', 'outstanding', 'studentsWithDue'));
    }


}
