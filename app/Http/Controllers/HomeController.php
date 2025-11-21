<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Exam;
use App\Models\Attendance;
use App\Models\FeesInformation;
use App\Models\Bus;
use App\Models\Route;
use App\Models\StudentBusAssignment;
use App\Models\ExamResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


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
    // Basic Counts
    $totalStudents = Student::count();
    $totalTeachers = Teacher::count();
    $totalDepartments = Department::count();
    $totalEarnings = Student::sum('amount_paid');
    
    // Additional Statistics
    $totalSubjects = \App\Models\Subject::count();
    $totalExams = Exam::count();
    $totalEvents = Event::where('is_active', true)->count();
    $totalBuses = Bus::where('is_active', true)->count();
    
    // Fee Statistics
    $totalFeeCollected = FeesInformation::sum('fees_amount');
    $pendingFees = Student::where('balance', '>', 0)->sum('balance');
    $paidStudents = Student::where('balance', '<=', 0)->count();
    $pendingFeeStudents = Student::where('balance', '>', 0)->count();
    
    // Attendance Statistics (Today)
    $todayAttendance = Attendance::whereDate('attendance_date', today())->count();
    $todayPresent = Attendance::whereDate('attendance_date', today())->where('status', 'present')->count();
    $todayAbsent = Attendance::whereDate('attendance_date', today())->where('status', 'absent')->count();
    $attendanceRate = $totalStudents > 0 ? round(($todayPresent / $totalStudents) * 100, 1) : 0;
    
    // Transport Statistics
    $studentsWithTransport = StudentBusAssignment::where('status', 'active')->count();
    $activeRoutes = Route::where('is_active', true)->count();
    
    // Get students grouped by gender
    $studentsByGender = Student::selectRaw('gender, COUNT(*) as total')
        ->groupBy('gender')
        ->pluck('total', 'gender');

    // Get monthly stats for students and teachers
    $studentMonthly = Student::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    $teacherMonthly = Teacher::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();
    
    // Fee collection monthly stats
    $feeMonthly = FeesInformation::selectRaw('MONTH(paid_date) as month, SUM(fees_amount) as total')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    // Fill 12 months with zeros if no data
    $months = range(1, 12);
    $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $studentData = [];
    $teacherData = [];
    $feeData = [];

    foreach ($months as $m) {
        $studentData[] = (int)($studentMonthly[$m] ?? 0);
        $teacherData[] = (int)($teacherMonthly[$m] ?? 0);
        $feeData[] = (float)($feeMonthly[$m] ?? 0);
    }
    
    // Ensure arrays are not empty
    if (empty($studentData)) {
        $studentData = array_fill(0, 12, 0);
    }
    if (empty($teacherData)) {
        $teacherData = array_fill(0, 12, 0);
    }
    if (empty($feeData)) {
        $feeData = array_fill(0, 12, 0);
    }

    // Extract gender counts safely
    $boys = $studentsByGender['Male'] ?? 0;
    $girls = $studentsByGender['Female'] ?? 0;
    
    // Recent Activity
    $recentStudents = Student::orderBy('created_at', 'desc')->limit(5)->get();
    $recentTeachers = Teacher::orderBy('created_at', 'desc')->limit(5)->get();
    $recentNotifications = Notification::where(function($query) {
        $query->where('user_id', auth()->id())
              ->orWhereNull('user_id');
    })->orderBy('created_at', 'desc')->limit(5)->get();
    
    // Upcoming Events (next 7 days)
    $upcomingEvents = Event::where('is_active', true)
        ->where('start_date', '>=', today())
        ->where('start_date', '<=', today()->addDays(7))
        ->orderBy('start_date', 'asc')
        ->limit(5)
        ->get();
    
    // Top Performing Students (if exam results exist)
    $topStudents = ExamResult::select('student_id', DB::raw('AVG(marks) as avg_marks'))
        ->groupBy('student_id')
        ->orderBy('avg_marks', 'desc')
        ->limit(5)
        ->with(['student' => function($query) {
            $query->select('id', 'first_name', 'last_name');
        }])
        ->get();
    
    // Recent Fee Payments
    $recentPayments = FeesInformation::with('student')
        ->whereNotNull('student_id')
        ->orderBy('paid_date', 'desc')
        ->limit(5)
        ->get();

    // Pass everything to view
    return view('dashboard.home', compact(
        'totalStudents',
        'totalTeachers',
        'totalDepartments',
        'boys',
        'girls',
        'studentData',
        'teacherData',
        'totalEarnings',
        'totalSubjects',
        'totalExams',
        'totalEvents',
        'totalBuses',
        'totalFeeCollected',
        'pendingFees',
        'paidStudents',
        'pendingFeeStudents',
        'todayAttendance',
        'todayPresent',
        'todayAbsent',
        'attendanceRate',
        'studentsWithTransport',
        'activeRoutes',
        'monthLabels',
        'feeData',
        'recentStudents',
        'recentTeachers',
        'recentNotifications',
        'upcomingEvents',
        'topStudents',
        'recentPayments'
    ));
}



    /** profile user */
    public function userProfile()
    {
        return view('dashboard.profile');
    }
}
