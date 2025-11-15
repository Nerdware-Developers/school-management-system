<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Department;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    /** home dashboard */
    public function index()
{
    // Counts
    $totalStudents = Student::count();
    $totalTeachers = Teacher::count();
    $totalDepartments = Department::count();
    $totalEarnings = Student::sum('amount_paid'); // 💰 total actual earnings

    // Get students grouped by gender
    $studentsByGender = Student::selectRaw('gender, COUNT(*) as total')
        ->groupBy('gender')
        ->pluck('total', 'gender');

    // Get teacher monthly stats
    $studentMonthly = Student::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    $teacherMonthly = Teacher::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    // Fill 12 months with zeros if no data
    $months = range(1, 12);
    $studentData = [];
    $teacherData = [];

    foreach ($months as $m) {
        $studentData[] = $studentMonthly[$m] ?? 0;
        $teacherData[] = $teacherMonthly[$m] ?? 0;
    }

    // Extract gender counts safely
    $boys = $studentsByGender['Male'] ?? 0;
    $girls = $studentsByGender['Female'] ?? 0;

    // Pass everything to view
    return view('dashboard.home', compact(
        'totalStudents',
        'totalTeachers',
        'totalDepartments',
        'boys',
        'girls',
        'studentData',
        'teacherData',
        'totalEarnings'
    ));
}



    /** profile user */
    public function userProfile()
    {
        return view('dashboard.profile');
    }

    /** teacher dashboard */
    public function teacherDashboardIndex()
    {
        return view('dashboard.teacher_dashboard');
    }

    /** student dashboard */
    public function studentDashboardIndex()
    {
        return view('dashboard.student_dashboard');
    }
}
